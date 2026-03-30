<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
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
}
