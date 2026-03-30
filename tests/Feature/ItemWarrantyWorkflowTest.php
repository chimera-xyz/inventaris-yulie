<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemWarrantyWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_can_be_saved_without_warranty_and_detail_page_shows_explicit_status(): void
    {
        $this->signIn();
        Storage::fake('public');

        $category = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor kerja.',
        ]);

        $response = $this->post(route('items.store'), [
            'category_id' => $category->id,
            'name' => 'Monitor Tanpa Garansi',
            'status' => 'available',
            'purchase_date' => '2026-03-27',
            'has_warranty' => '0',
            'warranty_expiry' => '2027-03-27',
        ]);

        $response->assertRedirect();

        $item = Item::query()->firstOrFail();

        $this->assertFalse($item->has_warranty);
        $this->assertNull($item->warranty_expiry);

        $this->get(route('items.show', $item))
            ->assertOk()
            ->assertSee('Tidak ada garansi sejak pembelian');
    }

    public function test_updating_item_to_no_warranty_clears_existing_warranty_date(): void
    {
        $this->signIn();

        $category = Category::create([
            'code' => 'LAP',
            'name' => 'Laptop',
            'description' => 'Laptop kerja.',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'LAP-2026-0001',
            'name' => 'Laptop Drafting',
            'status' => 'available',
            'purchase_date' => '2026-03-27',
            'has_warranty' => true,
            'warranty_expiry' => '2027-03-27',
        ]);

        $this->put(route('items.update', $item), [
            'category_id' => $category->id,
            'name' => 'Laptop Drafting',
            'status' => 'available',
            'purchase_date' => '2026-03-27',
            'has_warranty' => '0',
        ])->assertRedirect(route('items.show', $item));

        $item->refresh();

        $this->assertFalse($item->has_warranty);
        $this->assertNull($item->warranty_expiry);
    }
}
