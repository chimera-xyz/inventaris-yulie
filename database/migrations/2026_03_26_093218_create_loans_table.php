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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loaned_by')->nullable()->constrained('users')->nullOnDelete(); // User yang meminjam
            $table->string('borrower_name', 200); // Nama peminjam
            $table->string('borrower_department')->nullable(); // Departemen peminjam
            $table->date('loan_date');
            $table->date('return_date')->nullable();
            $table->date('expected_return_date')->nullable(); // Tanggal pengembalian yang diharapkan
            $table->enum('status', ['active', 'returned', 'overdue'])->default('active');
            $table->text('notes')->nullable();
            $table->string('signature')->nullable(); // Path gambar tanda tangan
            $table->timestamps();

            $table->index('item_id');
            $table->index('loaned_by');
            $table->index('status');
            $table->index('loan_date');
            $table->index('return_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
