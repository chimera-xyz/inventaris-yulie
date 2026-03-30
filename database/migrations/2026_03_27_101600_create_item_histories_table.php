<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type', 40);
            $table->date('event_date');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('responsible_party', 150)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->timestamps();

            $table->index(['item_id', 'event_date']);
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_histories');
    }
};
