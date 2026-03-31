<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('INVENTARIS_ADMIN_EMAIL', 'admin@yulie.local');
        $adminName = env('INVENTARIS_ADMIN_NAME', 'Admin Inventaris');
        $adminPassword = env('INVENTARIS_ADMIN_PASSWORD', 'inventaris123');

        User::query()->updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => bcrypt($adminPassword),
            ]
        );
    }
}
