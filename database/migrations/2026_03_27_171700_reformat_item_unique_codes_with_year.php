<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->rebuildUniqueCodes(withYear: true);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->rebuildUniqueCodes(withYear: false);
    }

    private function rebuildUniqueCodes(bool $withYear): void
    {
        $categories = DB::table('categories')
            ->pluck('code', 'id');

        $items = DB::table('items')
            ->select(['id', 'category_id', 'unique_code', 'qr_code_image', 'created_at'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $counters = [];

        foreach ($items as $item) {
            $categoryCode = $categories[$item->category_id] ?? 'CAT';
            $year = (int) date('Y', strtotime((string) ($item->created_at ?? now())));
            $groupKey = $withYear
                ? "{$item->category_id}:{$year}"
                : (string) $item->category_id;

            $sequence = ($counters[$groupKey] ?? 0) + 1;
            $counters[$groupKey] = $sequence;

            $newCode = $withYear
                ? sprintf('%s-%d-%04d', $categoryCode, $year, $sequence)
                : sprintf('%s-%03d', $categoryCode, $sequence);

            $newQrPath = "qrcodes/{$newCode}.svg";

            Storage::disk('public')->put(
                $newQrPath,
                QrCode::encoding('UTF-8')
                    ->format('svg')
                    ->errorCorrection('H')
                    ->size(300)
                    ->margin(2)
                    ->generate($this->buildPublicScanUrl($newCode))
            );

            if (filled($item->qr_code_image) && $item->qr_code_image !== $newQrPath) {
                Storage::disk('public')->delete($item->qr_code_image);
            }

            DB::table('items')
                ->where('id', $item->id)
                ->update([
                    'unique_code' => $newCode,
                    'qr_code_image' => $newQrPath,
                ]);
        }
    }

    private function buildPublicScanUrl(string $uniqueCode): string
    {
        $baseUrl = rtrim((string) config('app.public_url', config('app.url', 'http://localhost')), '/');

        return $baseUrl . route('public.items.show', ['item' => $uniqueCode], false);
    }
};
