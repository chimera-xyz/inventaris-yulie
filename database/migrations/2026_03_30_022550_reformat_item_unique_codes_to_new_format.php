<?php

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Reformat item unique codes from KODE-TAHUN-NOMOR to KODE-NOMOR-TAHUN
     */
    public function up(): void
    {
        DB::beginTransaction();

        try {
            $items = Item::with('category')->get();
            $reformattedCount = 0;

            foreach ($items as $item) {
                if ($item->category) {
                    $parts = explode('-', $item->unique_code);

                    // Check if code is in old format KODE-TAHUN-NOMOR (3 parts)
                    if (count($parts) === 3 && is_numeric($parts[1]) && is_numeric($parts[2])) {
                        $categoryCode = $parts[0];
                        $year = $parts[1];
                        $number = $parts[2];

                        // Reformat to KODE-NOMOR-TAHUN
                        $newCode = sprintf('%s-%s-%s', $categoryCode, $number, $year);

                        // Update the item
                        $item->unique_code = $newCode;
                        $item->save();

                        $reformattedCount++;
                    }
                }
            }

            DB::commit();

            echo "Successfully reformatted {$reformattedCount} item codes from KODE-TAHUN-NOMOR to KODE-NOMOR-TAHUN format.\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error reformatting item codes: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     * Reformat item unique codes back from KODE-NOMOR-TAHUN to KODE-TAHUN-NOMOR
     */
    public function down(): void
    {
        DB::beginTransaction();

        try {
            $items = Item::with('category')->get();
            $reformattedCount = 0;

            foreach ($items as $item) {
                if ($item->category) {
                    $parts = explode('-', $item->unique_code);

                    // Check if code is in new format KODE-NOMOR-TAHUN (3 parts)
                    if (count($parts) === 3 && is_numeric($parts[1]) && is_numeric($parts[2])) {
                        $categoryCode = $parts[0];
                        $number = $parts[1];
                        $year = $parts[2];

                        // Reformat back to KODE-TAHUN-NOMOR
                        $oldCode = sprintf('%s-%s-%s', $categoryCode, $year, $number);

                        // Update the item
                        $item->unique_code = $oldCode;
                        $item->save();

                        $reformattedCount++;
                    }
                }
            }

            DB::commit();

            echo "Successfully reverted {$reformattedCount} item codes back to KODE-TAHUN-NOMOR format.\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error reverting item codes: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
};
