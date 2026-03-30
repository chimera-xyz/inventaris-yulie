<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => env('INVENTARIS_ADMIN_EMAIL', 'admin@yulie.local')],
            [
                'name' => env('INVENTARIS_ADMIN_NAME', 'Admin Inventaris'),
                'password' => env('INVENTARIS_ADMIN_PASSWORD', 'inventaris123'),
            ]
        );
    }
}
