<?php

namespace App\Support;

use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use ZipArchive;

class ItemPdfExportService
{
    public function exportSingle(Item $item): string
    {
        $workingDirectory = sys_get_temp_dir() . '/inventaris-asset-export-' . Str::uuid();
        File::ensureDirectoryExists($workingDirectory);

        $pdfPath = sys_get_temp_dir() . '/inventaris-asset-export-' . Str::uuid() . '.pdf';
        $this->generatePdfForItem($item, $workingDirectory, $pdfPath);
        File::deleteDirectory($workingDirectory);

        return $pdfPath;
    }

    public function export(Collection $items): string
    {
        $workingDirectory = sys_get_temp_dir() . '/inventaris-asset-export-' . Str::uuid();
        File::ensureDirectoryExists($workingDirectory);

        $zipPath = sys_get_temp_dir() . '/inventaris-asset-export-' . Str::uuid() . '.zip';
        $archive = new ZipArchive();
        $archive->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($items as $item) {
            $pdfPath = $this->generatePdfForItem($item, $workingDirectory);
            $archive->addFile($pdfPath, basename($pdfPath));
        }

        $archive->close();
        File::deleteDirectory($workingDirectory);

        return $zipPath;
    }

    private function generatePdfForItem(Item $item, string $workingDirectory, ?string $pdfPath = null): string
    {
        $baseName = Str::slug($item->unique_code . '-' . $item->name, '-') ?: $item->unique_code;
        $htmlPath = $workingDirectory . '/' . $baseName . '.html';
        $pdfPath ??= $workingDirectory . '/' . $baseName . '.pdf';

        $html = view('items.export-pdf', [
            'item' => $item,
            'logoDataUri' => $this->publicFileToDataUri(public_path('brand/yulie-sekuritas-logo.png')),
            'photoDataUris' => $item->photos
                ->map(fn ($photo) => $this->storageFileToDataUri($photo->path))
                ->filter()
                ->values(),
            'generatedAt' => now(),
        ])->render();

        File::put($htmlPath, $html);

        $process = new Process([
            (string) config('services.pdf.chrome_binary'),
            '--headless',
            '--disable-gpu',
            '--no-pdf-header-footer',
            '--print-to-pdf=' . $pdfPath,
            'file://' . $htmlPath,
        ]);

        $process->setTimeout(180);
        $process->mustRun();

        return $pdfPath;
    }

    private function publicFileToDataUri(string $path): ?string
    {
        if (! is_file($path)) {
            return null;
        }

        $mimeType = mime_content_type($path) ?: 'application/octet-stream';
        $contents = base64_encode((string) file_get_contents($path));

        return 'data:' . $mimeType . ';base64,' . $contents;
    }

    private function storageFileToDataUri(string $storagePath): ?string
    {
        return $this->publicFileToDataUri(storage_path('app/public/' . $storagePath));
    }
}
