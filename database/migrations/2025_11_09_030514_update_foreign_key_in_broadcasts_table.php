<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->unsignedBigInteger('broadcast_category_id')->nullable()->after('category_id');
        });

        $this->copyUsedCategoriesAndMapBroadcasts(
            sourceTable: 'categories',
            targetTable: 'broadcast_categories',
            sourceColumn: 'category_id',
            targetColumn: 'broadcast_category_id',
        );

        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::table('broadcasts', function (Blueprint $table) {
            $table->unsignedBigInteger('broadcast_category_id')->nullable(false)->change();
            $table->foreign('broadcast_category_id')
                ->references('id')
                ->on('broadcast_categories')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('user_id');
        });

        $this->copyUsedCategoriesAndMapBroadcasts(
            sourceTable: 'broadcast_categories',
            targetTable: 'categories',
            sourceColumn: 'broadcast_category_id',
            targetColumn: 'category_id',
        );

        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropForeign(['broadcast_category_id']);
            $table->dropColumn('broadcast_category_id');
        });

        Schema::table('broadcasts', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->cascadeOnDelete();
        });
    }

    /**
     * Copy only categories referenced by broadcasts and map them by slug.
     *
     * Preserving an ID is preferred when it is free. When that ID is already
     * occupied by a different slug, the target database assigns a new ID and
     * broadcasts are updated to that explicit mapped ID.
     */
    private function copyUsedCategoriesAndMapBroadcasts(
        string $sourceTable,
        string $targetTable,
        string $sourceColumn,
        string $targetColumn,
    ): void {
        $sourceCategories = DB::table($sourceTable)
            ->join('broadcasts', "{$sourceTable}.id", '=', "broadcasts.{$sourceColumn}")
            ->select([
                "{$sourceTable}.id",
                "{$sourceTable}.name",
                "{$sourceTable}.slug",
                "{$sourceTable}.color",
                "{$sourceTable}.created_at",
                "{$sourceTable}.updated_at",
            ])
            ->distinct()
            ->orderBy("{$sourceTable}.id")
            ->get();

        foreach ($sourceCategories as $sourceCategory) {
            $targetId = $this->resolveTargetCategoryId(
                targetTable: $targetTable,
                sourceCategory: $sourceCategory,
            );

            DB::table('broadcasts')
                ->where($sourceColumn, $sourceCategory->id)
                ->update([$targetColumn => $targetId]);
        }

        $unmappedCount = DB::table('broadcasts')
            ->whereNull($targetColumn)
            ->count();

        if ($unmappedCount > 0) {
            throw new RuntimeException(
                "Tidak dapat memetakan {$unmappedCount} penyiaran dari {$sourceTable} ke {$targetTable}."
            );
        }

        $missingTargetCount = DB::table('broadcasts')
            ->leftJoin($targetTable, "broadcasts.{$targetColumn}", '=', "{$targetTable}.id")
            ->whereNull("{$targetTable}.id")
            ->count();

        if ($missingTargetCount > 0) {
            throw new RuntimeException(
                "Ditemukan {$missingTargetCount} penyiaran dengan kategori tujuan yang tidak valid."
            );
        }
    }

    private function resolveTargetCategoryId(string $targetTable, object $sourceCategory): int
    {
        $sameSlugTarget = DB::table($targetTable)
            ->where('slug', $sourceCategory->slug)
            ->first();

        if ($sameSlugTarget !== null) {
            return (int) $sameSlugTarget->id;
        }

        $attributes = [
            'name' => $sourceCategory->name,
            'slug' => $sourceCategory->slug,
            'color' => $sourceCategory->color,
            'created_at' => $sourceCategory->created_at,
            'updated_at' => $sourceCategory->updated_at,
        ];

        $sourceIdIsAvailable = ! DB::table($targetTable)
            ->where('id', $sourceCategory->id)
            ->exists();

        if ($sourceIdIsAvailable) {
            DB::table($targetTable)->insert(['id' => $sourceCategory->id] + $attributes);

            return (int) $sourceCategory->id;
        }

        return (int) DB::table($targetTable)->insertGetId($attributes);
    }
};
