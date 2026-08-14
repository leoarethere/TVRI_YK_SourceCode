<?php

namespace Tests\Unit;

use App\Support\PublicStorageWriter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class PublicStorageWriterTest extends TestCase
{
    public function test_it_writes_contents_to_the_public_disk(): void
    {
        Storage::fake('public');

        PublicStorageWriter::put('images/example.txt', 'stored contents');

        Storage::disk('public')->assertExists('images/example.txt', 'stored contents');
    }

    public function test_it_throws_when_the_public_disk_rejects_a_write(): void
    {
        $disk = Mockery::mock(FilesystemAdapter::class);
        $disk->shouldReceive('put')
            ->once()
            ->with('images/example.txt', 'stored contents')
            ->andReturnFalse();

        Storage::shouldReceive('disk')
            ->once()
            ->with('public')
            ->andReturn($disk);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Gagal menyimpan file ke public storage: images/example.txt');

        PublicStorageWriter::put('images/example.txt', 'stored contents');
    }
}
