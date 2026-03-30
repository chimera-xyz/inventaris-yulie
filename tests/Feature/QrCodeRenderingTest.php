<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QrCodeRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_code_url_prefers_public_url_configuration(): void
    {
        config()->set('app.public_url', 'http://inventory-test.local:8000');

        $category = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor kerja.',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'MON-001',
            'name' => 'Dell UltraSharp 27',
            'brand' => 'Dell',
            'status' => 'available',
            'location' => 'Ruang Finance',
        ]);

        $this->assertSame('http://inventory-test.local:8000/scan/MON-001', $item->qr_code_url);
    }

    public function test_item_detail_repairs_legacy_qr_file_and_renders_svg_path(): void
    {
        $this->signIn();

        Storage::fake('public');

        $category = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor kerja.',
        ]);

        $legacyPath = 'qrcodes/MON-001.png';
        Storage::disk('public')->put($legacyPath, '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

        $item = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'MON-001',
            'name' => 'Dell UltraSharp 27',
            'brand' => 'Dell',
            'status' => 'available',
            'location' => 'Ruang Finance',
            'qr_code_image' => $legacyPath,
        ]);

        $response = $this->get(route('items.show', $item));

        $response->assertOk();
        $response->assertSee('storage/qrcodes/MON-001.svg', false);

        $item->refresh();

        $this->assertSame('qrcodes/MON-001.svg', $item->qr_code_image);
        Storage::disk('public')->assertExists('qrcodes/MON-001.svg');
        Storage::disk('public')->assertMissing($legacyPath);
    }
}
