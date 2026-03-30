<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_with_inventory_data(): void
    {
        $this->signIn();

        $category = Category::create([
            'code' => 'LAP',
            'name' => 'Laptop',
            'description' => 'Perangkat kerja mobile.',
        ]);

        Item::create([
            'category_id' => $category->id,
            'unique_code' => 'LAP-001',
            'name' => 'Dell Latitude 5440',
            'brand' => 'Dell',
            'model' => 'Latitude 5440',
            'status' => 'available',
            'location' => 'Ruang IT',
            'price' => 15500000,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee('Dell Latitude 5440');
        $response->assertSee('LAP-001');
    }

    public function test_dashboard_only_shows_three_latest_activities_and_links_to_full_page(): void
    {
        $user = $this->signIn();

        $category = Category::create([
            'code' => 'LAP',
            'name' => 'Laptop',
            'description' => 'Perangkat kerja mobile.',
        ]);

        $item = Item::withoutEvents(function () use ($category) {
            return Item::create([
                'category_id' => $category->id,
                'unique_code' => 'LAP-001',
                'name' => 'Dell Latitude 5440',
                'brand' => 'Dell',
                'model' => 'Latitude 5440',
                'status' => 'available',
                'location' => 'Ruang IT',
                'price' => 15500000,
            ]);
        });

        ItemLog::unguarded(function () use ($item, $user) {
            foreach (range(1, 4) as $number) {
                ItemLog::create([
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                    'action' => 'update',
                    'notes' => 'Log dashboard ' . $number,
                    'created_at' => now()->addMinutes($number),
                    'updated_at' => now()->addMinutes($number),
                ]);
            }
        });

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Log dashboard 4');
        $response->assertSee('Log dashboard 3');
        $response->assertSee('Log dashboard 2');
        $response->assertDontSee('Log dashboard 1');
        $response->assertSee('Lihat Semua Aktivitas');
        $response->assertSee(route('activities.index'), false);
    }
}
