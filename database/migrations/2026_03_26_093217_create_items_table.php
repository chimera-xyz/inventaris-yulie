<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('unique_code', 20)->unique(); // MON-2026-0001, KEY-2026-0002, etc
            $table->string('name', 200);
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->string('location', 200)->nullable(); // Ruangan, server rack, etc
            $table->enum('status', ['available', 'in_use', 'broken', 'maintenance', 'lost'])->default('available');
            $table->text('notes')->nullable();
            $table->string('qr_code_image')->nullable(); // Path ke gambar QR code
            $table->timestamps();
            $table->softDeletes();

            $table->index('unique_code');
            $table->index('status');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
