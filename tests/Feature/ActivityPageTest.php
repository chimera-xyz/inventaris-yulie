<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_page_is_paginated(): void
    {
        $user = $this->signIn();

        $category = Category::create([
            'code' => 'LAP',
            'name' => 'Laptop',
            'description' => 'Laptop kerja.',
        ]);

        $item = Item::withoutEvents(function () use ($category) {
            return Item::create([
                'category_id' => $category->id,
                'unique_code' => 'LAP-001',
                'name' => 'Lenovo ThinkPad',
                'brand' => 'Lenovo',
                'status' => 'available',
                'location' => 'IT',
            ]);
        });

        ItemLog::unguarded(function () use ($item, $user) {
            foreach (range(1, 13) as $number) {
                ItemLog::create([
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                    'action' => 'update',
                    'notes' => 'Aktivitas ' . str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                    'created_at' => now()->addMinutes($number),
                    'updated_at' => now()->addMinutes($number),
                ]);
            }
        });

        $firstPage = $this->get(route('activities.index'));

        $firstPage->assertOk();
        $firstPage->assertSee('Aktivitas 13');
        $firstPage->assertDontSee('Aktivitas 01');

        $secondPage = $this->get(route('activities.index', ['page' => 2]));

        $secondPage->assertOk();
        $secondPage->assertSee('Aktivitas 01');
    }
}
