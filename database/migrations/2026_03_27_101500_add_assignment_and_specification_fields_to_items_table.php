<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->text('specifications')->nullable()->after('notes');
            $table->string('assigned_user_name', 150)->nullable()->after('specifications');
            $table->string('assigned_division', 150)->nullable()->after('assigned_user_name');
            $table->string('assigned_phone', 30)->nullable()->after('assigned_division');
            $table->date('assigned_since')->nullable()->after('assigned_phone');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'specifications',
                'assigned_user_name',
                'assigned_division',
                'assigned_phone',
                'assigned_since',
            ]);
        });
    }
};
