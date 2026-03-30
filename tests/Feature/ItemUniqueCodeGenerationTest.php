<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemUniqueCodeGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_items_receive_category_year_sequence_codes(): void
    {
        $this->signIn();
        Storage::fake('public');
        $this->travelTo(Carbon::parse('2026-03-27 09:00:00'));

        $monitor = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor kerja.',
        ]);

        $this->post(route('items.store'), [
            'category_id' => $monitor->id,
            'name' => 'Monitor A',
            'status' => 'available',
        ])->assertRedirect();

        $this->post(route('items.store'), [
            'category_id' => $monitor->id,
            'name' => 'Monitor B',
            'status' => 'available',
        ])->assertRedirect();

        $this->assertSame(
            ['MON-2026-0001', 'MON-2026-0002'],
            Item::query()->orderBy('id')->pluck('unique_code')->all()
        );

        $this->travelBack();
    }

    public function test_item_keeps_original_recording_year_when_category_changes(): void
    {
        $this->signIn();
        Storage::fake('public');

        $monitor = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor kerja.',
        ]);

        $lan = Category::create([
            'code' => 'LAN',
            'name' => 'LAN',
            'description' => 'Perangkat jaringan.',
        ]);

        $existingLanItem = Item::create([
            'category_id' => $lan->id,
            'unique_code' => 'LAN-2026-0001',
            'name' => 'Switch Core',
            'status' => 'available',
            'created_at' => Carbon::parse('2026-03-26 09:00:00'),
            'updated_at' => Carbon::parse('2026-03-26 09:00:00'),
        ]);

        $item = Item::create([
            'category_id' => $monitor->id,
            'unique_code' => 'MON-2026-0001',
            'name' => 'Monitor Trading',
            'status' => 'available',
            'created_at' => Carbon::parse('2026-03-27 09:00:00'),
            'updated_at' => Carbon::parse('2026-03-27 09:00:00'),
        ]);

        $this->travelTo(Carbon::parse('2027-01-15 10:00:00'));

        $this->put(route('items.update', $item), [
            'category_id' => $lan->id,
            'name' => 'Monitor Trading',
            'status' => 'available',
        ])->assertRedirect(route('items.show', 'LAN-2026-0002'));

        $item->refresh();
        $existingLanItem->refresh();

        $this->assertSame('LAN-2026-0001', $existingLanItem->unique_code);
        $this->assertSame('LAN-2026-0002', $item->unique_code);

        $this->travelBack();
    }
}
