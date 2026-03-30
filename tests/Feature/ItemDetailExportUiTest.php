<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailExportUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_detail_page_renders_single_asset_export_controls(): void
    {
        $this->signIn();

        $category = Category::create([
            'code' => 'LAP',
            'name' => 'Laptop',
            'description' => 'Laptop kerja.',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'LAP-001',
            'name' => 'Lenovo ThinkPad',
            'brand' => 'Lenovo',
            'status' => 'available',
            'location' => 'IT',
        ]);

        $response = $this->get(route('items.show', $item));

        $response->assertOk();
        $response->assertSee('Export Asset');
        $response->assertSee('Excel');
        $response->assertSee('PDF');
        $response->assertSee(route('items.export-selected'), false);
        $response->assertSee('name="item_ids[]" value="' . $item->id . '"', false);
    }
}
