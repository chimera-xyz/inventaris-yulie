<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryNotesTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_detail_uses_saved_notes_instead_of_hardcoded_operational_copy(): void
    {
        $this->signIn();

        $category = Category::create([
            'code' => 'MON',
            'name' => 'Monitor',
            'description' => 'Catatan internal tim: dipakai untuk monitor kerja user tetap.',
        ]);

        $response = $this->get(route('categories.show', $category));

        $response->assertOk();
        $response->assertSee('Catatan Kategori');
        $response->assertSee('Catatan internal tim: dipakai untuk monitor kerja user tetap.');
        $response->assertDontSee('Pertahankan kode kategori supaya numbering asset tetap stabil.');
    }
}
