<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditBroadcastCategories extends Command
{
    protected $signature = 'broadcasts:audit-categories
        {--map=* : Pemetaan ID eksplisit FROM:TO; dapat diulang}
        {--apply : Terapkan pemetaan eksplisit ke database}';

    protected $description = 'Audit pemetaan kategori penyiaran dan terapkan remap ID hanya jika diminta secara eksplisit';

    public function handle(): int
    {
        if (! $this->schemaIsSupported()) {
            return self::FAILURE;
        }

        $missingTargetCount = $this->renderAudit();

        $mappings = $this->parseMappings((array) $this->option('map'));

        if ($mappings === null) {
            return self::INVALID;
        }

        if ($mappings === []) {
            if ($this->option('apply')) {
                $this->error('Opsi --apply memerlukan setidaknya satu --map=FROM:TO.');

                return self::INVALID;
            }

            if ($missingTargetCount > 0) {
                $this->error("Audit gagal: {$missingTargetCount} penyiaran mengacu ke kategori target yang hilang.");

                return self::FAILURE;
            }

            $this->newLine();
            $this->info('Audit selesai dalam mode baca-saja; tidak ada data yang diubah.');

            return self::SUCCESS;
        }

        $previewRows = $this->buildMappingPreview($mappings);

        if ($previewRows === null) {
            return self::INVALID;
        }

        $this->newLine();
        $this->table(
            ['Dari ID', 'Slug saat ini', 'Ke ID', 'Slug tujuan', 'Jumlah penyiaran'],
            $previewRows,
        );

        if (! $this->option('apply')) {
            $this->info('Pratinjau saja. Tambahkan --apply untuk menerapkan pemetaan eksplisit tersebut.');

            return $missingTargetCount > 0 ? self::FAILURE : self::SUCCESS;
        }

        $updatedCount = DB::transaction(function () use ($mappings): int {
            $caseExpression = 'CASE broadcast_category_id ';

            foreach ($mappings as $fromId => $toId) {
                $caseExpression .= "WHEN {$fromId} THEN {$toId} ";
            }

            $caseExpression .= 'ELSE broadcast_category_id END';

            return DB::table('broadcasts')
                ->whereIn('broadcast_category_id', array_keys($mappings))
                ->update([
                    'broadcast_category_id' => DB::raw($caseExpression),
                ]);
        });

        $this->info("Pemetaan selesai. {$updatedCount} penyiaran diperbarui.");

        $remainingMissingTargets = $this->countMissingTargets();

        if ($remainingMissingTargets > 0) {
            $this->error(
                "Audit tetap gagal: {$remainingMissingTargets} penyiaran masih mengacu ke kategori target yang hilang."
            );

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function schemaIsSupported(): bool
    {
        foreach (['broadcasts', 'broadcast_categories', 'categories'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->error("Tabel {$table} tidak ditemukan.");

                return false;
            }
        }

        if (! Schema::hasColumn('broadcasts', 'broadcast_category_id')) {
            $this->error('Kolom broadcasts.broadcast_category_id tidak ditemukan. Jalankan migration terlebih dahulu.');

            return false;
        }

        return true;
    }

    private function renderAudit(): int
    {
        $rows = DB::table('broadcasts')
            ->leftJoin(
                'broadcast_categories as current_category',
                'current_category.id',
                '=',
                'broadcasts.broadcast_category_id',
            )
            ->leftJoin(
                'categories as legacy_category',
                'legacy_category.id',
                '=',
                'broadcasts.broadcast_category_id',
            )
            ->leftJoin(
                'broadcast_categories as slug_candidate',
                'slug_candidate.slug',
                '=',
                'legacy_category.slug',
            )
            ->select([
                'broadcasts.broadcast_category_id as current_id',
                'current_category.name as current_name',
                'current_category.slug as current_slug',
                'legacy_category.slug as legacy_slug',
                'slug_candidate.id as candidate_id',
                DB::raw('COUNT(broadcasts.id) as broadcasts_count'),
            ])
            ->groupBy([
                'broadcasts.broadcast_category_id',
                'current_category.name',
                'current_category.slug',
                'legacy_category.slug',
                'slug_candidate.id',
            ])
            ->orderBy('broadcasts.broadcast_category_id')
            ->get();

        if ($rows->isEmpty()) {
            $this->info('Tidak ada data penyiaran untuk diaudit.');

            return 0;
        }

        $this->table(
            ['ID aktif', 'Kategori aktif', 'Slug aktif', 'Slug legacy di ID sama', 'Kandidat slug', 'Program', 'Status'],
            $rows->map(function (object $row): array {
                $status = match (true) {
                    $row->current_slug === null => 'TARGET HILANG',
                    $row->legacy_slug === null => 'Tanpa pembanding legacy',
                    $row->legacy_slug === $row->current_slug => 'Cocok berdasarkan slug',
                    default => 'PERLU DITINJAU',
                };

                $candidate = $row->candidate_id === null
                    ? '-'
                    : '#'.(int) $row->candidate_id;

                return [
                    (int) $row->current_id,
                    $row->current_name ?? '-',
                    $row->current_slug ?? '-',
                    $row->legacy_slug ?? '-',
                    $candidate,
                    (int) $row->broadcasts_count,
                    $status,
                ];
            })->all(),
        );

        $this->warn('Perbedaan slug hanya ditandai untuk audit; command tidak menebak atau menerapkan remap otomatis.');

        return $rows
            ->whereNull('current_slug')
            ->sum('broadcasts_count');
    }

    private function countMissingTargets(): int
    {
        return DB::table('broadcasts')
            ->leftJoin(
                'broadcast_categories',
                'broadcast_categories.id',
                '=',
                'broadcasts.broadcast_category_id',
            )
            ->whereNull('broadcast_categories.id')
            ->count();
    }

    /**
     * @param  array<int, string>  $rawMappings
     * @return array<int, int>|null
     */
    private function parseMappings(array $rawMappings): ?array
    {
        $mappings = [];

        foreach ($rawMappings as $rawMapping) {
            if (! preg_match('/^\s*(\d+)\s*:\s*(\d+)\s*$/', $rawMapping, $matches)) {
                $this->error("Format pemetaan tidak valid: {$rawMapping}. Gunakan --map=FROM:TO dengan ID numerik.");

                return null;
            }

            $fromId = (int) $matches[1];
            $toId = (int) $matches[2];

            if ($fromId < 1 || $toId < 1 || $fromId === $toId) {
                $this->error("Pemetaan {$rawMapping} tidak valid; ID harus positif dan FROM harus berbeda dari TO.");

                return null;
            }

            if (isset($mappings[$fromId]) && $mappings[$fromId] !== $toId) {
                $this->error("ID sumber {$fromId} dipetakan ke lebih dari satu tujuan.");

                return null;
            }

            $mappings[$fromId] = $toId;
        }

        return $mappings;
    }

    /**
     * @param  array<int, int>  $mappings
     * @return array<int, array<int, int|string>>|null
     */
    private function buildMappingPreview(array $mappings): ?array
    {
        $rows = [];

        foreach ($mappings as $fromId => $toId) {
            $target = DB::table('broadcast_categories')->find($toId);

            if ($target === null) {
                $this->error("Kategori tujuan broadcast_categories.id={$toId} tidak ditemukan.");

                return null;
            }

            $source = DB::table('broadcast_categories')->find($fromId);
            $count = DB::table('broadcasts')
                ->where('broadcast_category_id', $fromId)
                ->count();

            $rows[] = [
                $fromId,
                $source->slug ?? '(target lama tidak ada)',
                $toId,
                $target->slug,
                $count,
            ];
        }

        return $rows;
    }
}
