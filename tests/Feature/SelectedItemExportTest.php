<?php

namespace Tests\Feature;

use App\Exports\ItemsExport;
use App\Models\Category;
use App\Models\Item;
use App\Support\ItemPdfExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\MockInterface;
use Tests\TestCase;

class SelectedItemExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_export_selected_assets_to_excel(): void
    {
        $this->signIn();
        Excel::fake();

        [$selectedA, $selectedB, $unselected] = $this->seedItemsForExport();

        $response = $this->post(route('items.export-selected'), [
            'format' => 'excel',
            'item_ids' => [$selectedA->id, $selectedB->id],
        ]);

        $response->assertOk();

        Excel::assertDownloaded(
            'inventaris-it-selected-' . now()->format('Y-m-d') . '.xlsx',
            function (ItemsExport $export) use ($selectedA, $selectedB, $unselected) {
                $exportedIds = $export->collection()->pluck('id')->values()->all();

                return $exportedIds === [$selectedA->id, $selectedB->id]
                    && ! in_array($unselected->id, $exportedIds, true);
            }
        );
    }

    public function test_authenticated_user_can_export_selected_assets_to_pdf_zip(): void
    {
        $this->signIn();

        [$selectedA, $selectedB] = $this->seedItemsForExport();
        $zipPath = tempnam(sys_get_temp_dir(), 'asset-export-test-') . '.zip';
        file_put_contents($zipPath, 'zip');

        $this->mock(ItemPdfExportService::class, function (MockInterface $mock) use ($zipPath, $selectedA, $selectedB) {
            $mock->shouldReceive('export')
                ->once()
                ->withArgs(function ($items) use ($selectedA, $selectedB) {
                    return $items->pluck('id')->values()->all() === [$selectedA->id, $selectedB->id];
                })
                ->andReturn($zipPath);
        });

        $response = $this->post(route('items.export-selected'), [
            'format' => 'pdf',
            'item_ids' => [$selectedA->id, $selectedB->id],
        ]);

        $response->assertDownload('inventaris-it-pdf-' . now()->format('Y-m-d') . '.zip');
    }

    public function test_authenticated_user_can_export_single_asset_to_pdf_without_zip(): void
    {
        $this->signIn();

        [$selectedItem] = $this->seedItemsForExport();
        $pdfPath = tempnam(sys_get_temp_dir(), 'asset-export-test-') . '.pdf';
        file_put_contents($pdfPath, 'pdf');

        $this->mock(ItemPdfExportService::class, function (MockInterface $mock) use ($pdfPath, $selectedItem) {
            $mock->shouldReceive('exportSingle')
                ->once()
                ->withArgs(fn ($item) => $item->is($selectedItem))
                ->andReturn($pdfPath);

            $mock->shouldNotReceive('export');
        });

        $response = $this->post(route('items.export-selected'), [
            'format' => 'pdf',
            'item_ids' => [$selectedItem->id],
        ]);

        $response->assertDownload('inventaris-it-' . strtolower($selectedItem->unique_code) . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * @return array<int, Item>
     */
    private function seedItemsForExport(): array
    {
        $category = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor kerja.',
        ]);

        $selectedA = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'MON-001',
            'name' => 'Monitor A',
            'status' => 'available',
            'location' => 'Lantai 1',
        ]);

        $selectedB = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'MON-002',
            'name' => 'Monitor B',
            'status' => 'maintenance',
            'location' => 'Lantai 2',
        ]);

        $unselected = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'MON-003',
            'name' => 'Monitor C',
            'status' => 'broken',
            'location' => 'Gudang',
        ]);

        return [$selectedA, $selectedB, $unselected];
    }
}
