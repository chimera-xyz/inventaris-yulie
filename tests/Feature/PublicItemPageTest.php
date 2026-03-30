<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicItemPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_item_page_is_accessible_without_auth_and_uses_public_scan_url(): void
    {
        config(['app.public_url' => 'http://192.168.1.10:8000']);

        $this->signIn();

        $category = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor kerja.',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'MON-010',
            'name' => 'Dell UltraSharp',
            'status' => 'maintenance',
            'location' => 'Ruang Trading',
            'specifications' => "Panel: 27 inci\nResolusi: 4K",
            'assigned_user_name' => 'Rina',
            'assigned_division' => 'Finance',
            'assigned_phone' => '08123456789',
        ]);

        $item->histories()->create([
            'event_type' => 'maintenance',
            'event_date' => '2026-03-27',
            'title' => 'Pembersihan panel',
            'description' => 'Unit dibersihkan dan diuji ulang.',
        ]);

        $this->post(route('logout'));

        $response = $this->get(route('public.items.show', $item));

        $response->assertOk();
        $response->assertSee('Dell UltraSharp');
        $response->assertSee('Pembersihan panel');
        $response->assertDontSee('Edit Asset');
        $this->assertSame('http://192.168.1.10:8000/scan/MON-010', $item->qr_code_url);
    }
}
