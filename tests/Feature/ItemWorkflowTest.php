<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_items_index_filters_by_status_and_search_without_breaking_on_empty_values(): void
    {
        $this->signIn();

        $laptopCategory = Category::create([
            'code' => 'LAP',
            'name' => 'Laptop',
            'description' => 'Laptop kerja.',
        ]);

        $monitorCategory = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor meja.',
        ]);

        Item::create([
            'category_id' => $laptopCategory->id,
            'unique_code' => 'LAP-001',
            'name' => 'Lenovo ThinkPad',
            'brand' => 'Lenovo',
            'status' => 'available',
            'location' => 'IT',
        ]);

        Item::create([
            'category_id' => $monitorCategory->id,
            'unique_code' => 'MON-001',
            'name' => 'Samsung Monitor',
            'brand' => 'Samsung',
            'status' => 'broken',
            'location' => 'Gudang',
        ]);

        $response = $this->get(route('items.index', [
            'search' => 'ThinkPad',
            'status' => 'available',
            'category' => '',
        ]));

        $response->assertOk();
        $response->assertSee('Lenovo ThinkPad');
        $response->assertSee('LAP-001');
        $response->assertDontSee('Samsung Monitor');
        $response->assertDontSee('MON-001');
    }
}
