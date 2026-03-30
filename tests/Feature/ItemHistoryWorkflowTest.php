<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemHistoryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_item_history_entry(): void
    {
        $this->signIn();

        $category = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor kerja.',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'MON-011',
            'name' => 'LG 27',
            'status' => 'available',
        ]);

        $response = $this->post(route('items.histories.store', $item), [
            'event_type' => 'service',
            'event_date' => '2026-03-27',
            'title' => 'Servis adaptor',
            'description' => 'Adaptor diganti.',
            'responsible_party' => 'CV Elektronik',
            'contact_phone' => '021-555-000',
        ]);

        $response->assertRedirect(route('items.show', $item));
        $this->assertDatabaseHas('item_histories', [
            'item_id' => $item->id,
            'event_type' => 'service',
            'title' => 'Servis adaptor',
        ]);
    }

    public function test_authenticated_user_can_edit_item_history_entry(): void
    {
        $this->signIn();

        $category = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor kerja.',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'MON-012',
            'name' => 'LG 32',
            'status' => 'maintenance',
        ]);

        $history = $item->histories()->create([
            'event_type' => 'maintenance',
            'event_date' => '2026-03-27',
            'title' => 'Maitenance panel',
            'description' => 'Typo awal.',
        ]);

        $response = $this->put(route('items.histories.update', [$item, $history]), [
            'event_type' => 'maintenance',
            'event_date' => '2026-03-28',
            'title' => 'Maintenance panel',
            'description' => 'Typo sudah diperbaiki.',
            'responsible_party' => 'Teknisi Internal',
            'contact_phone' => '0812000000',
        ]);

        $response->assertRedirect(route('items.show', $item));
        $this->assertDatabaseHas('item_histories', [
            'id' => $history->id,
            'title' => 'Maintenance panel',
            'description' => 'Typo sudah diperbaiki.',
            'responsible_party' => 'Teknisi Internal',
        ]);
    }
}
