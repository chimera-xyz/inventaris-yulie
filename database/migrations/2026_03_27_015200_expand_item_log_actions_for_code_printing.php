<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $actions = [
        'create',
        'update',
        'delete',
        'status_change',
        'loan',
        'return',
        'qr_generated',
        'qr_printed',
        'code_printed',
    ];

    public function up(): void
    {
        $this->updateActionColumn($this->actions);
    }

    public function down(): void
    {
        $actions = array_values(array_filter(
            $this->actions,
            fn (string $action) => $action !== 'code_printed'
        ));

        $this->updateActionColumn($actions, true);
    }

    private function updateActionColumn(array $actions, bool $downgrading = false): void
    {
        if (! Schema::hasTable('item_logs')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            $this->rebuildSqliteTable($actions, $downgrading);

            return;
        }

        Schema::table('item_logs', function (Blueprint $table) use ($actions) {
            $table->enum('action', $actions)->change();
        });
    }

    private function rebuildSqliteTable(array $actions, bool $downgrading = false): void
    {
        Schema::create('item_logs_new', function (Blueprint $table) use ($actions) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('action', $actions);
            $table->string('field', 100)->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('notes')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index('item_id');
            $table->index('action');
            $table->index('created_at');
        });

        $actionSelect = $downgrading
            ? "CASE WHEN action = 'code_printed' THEN 'qr_printed' ELSE action END"
            : 'action';

        DB::statement("
            INSERT INTO item_logs_new (
                id, item_id, user_id, action, field, old_value, new_value, notes, ip_address, user_agent, created_at, updated_at
            )
            SELECT
                id, item_id, user_id, {$actionSelect}, field, old_value, new_value, notes, ip_address, user_agent, created_at, updated_at
            FROM item_logs
        ");

        Schema::drop('item_logs');
        Schema::rename('item_logs_new', 'item_logs');
    }
};
