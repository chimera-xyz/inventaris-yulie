<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemPhotoWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_can_be_created_with_optional_photos(): void
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
            'name' => 'Xiaomi 27',
            'brand' => 'Xiaomi',
            'status' => 'available',
            'location' => 'Backoffice',
            'photos' => [
                UploadedFile::fake()->image('gallery-1.jpg'),
                UploadedFile::fake()->image('gallery-2.jpg'),
                UploadedFile::fake()->image('camera-1.jpg'),
            ],
        ]);

        $item = Item::query()->with('photos')->firstOrFail();

        $response->assertRedirect(route('items.show', $item));
        $this->assertCount(3, $item->photos);

        foreach ($item->photos as $photo) {
            Storage::disk('public')->assertExists($photo->path);
        }
    }

    public function test_selected_item_photos_can_be_deleted_from_edit_screen(): void
    {
        $this->signIn();

        Storage::fake('public');

        $category = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Monitor kerja.',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'unique_code' => 'MON-099',
            'name' => 'Monitor Editing',
            'status' => 'available',
        ]);

        $firstPhoto = $item->photos()->create([
            'path' => 'item-photos/' . $item->id . '/photo-1.jpg',
            'sort_order' => 1,
        ]);

        $secondPhoto = $item->photos()->create([
            'path' => 'item-photos/' . $item->id . '/photo-2.jpg',
            'sort_order' => 2,
        ]);

        Storage::disk('public')->put($firstPhoto->path, 'fake-image-1');
        Storage::disk('public')->put($secondPhoto->path, 'fake-image-2');

        $response = $this->delete(route('items.photos.destroy', $item), [
            'photo_ids' => [$firstPhoto->id],
        ]);

        $response->assertRedirect(route('items.edit', $item));
        $this->assertDatabaseMissing('item_photos', ['id' => $firstPhoto->id]);
        $this->assertDatabaseHas('item_photos', ['id' => $secondPhoto->id]);
        Storage::disk('public')->assertMissing($firstPhoto->path);
        Storage::disk('public')->assertExists($secondPhoto->path);
    }
}
