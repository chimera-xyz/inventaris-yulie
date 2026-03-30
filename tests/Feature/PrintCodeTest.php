<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_print_code_page_renders_and_logs_code_printed_action(): void
    {
        $this->signIn();

        $category = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor kerja.',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'MON-002',
            'name' => 'Xiaomi 2026',
            'brand' => 'Xiaomi',
            'status' => 'available',
            'location' => 'Backoffice',
        ]);

        $response = $this->get(route('items.print-code', $item));

        $response->assertOk();
        $response->assertSee('MON-002');

        $this->assertDatabaseHas('item_logs', [
            'item_id' => $item->id,
            'action' => 'code_printed',
        ]);
    }
}
