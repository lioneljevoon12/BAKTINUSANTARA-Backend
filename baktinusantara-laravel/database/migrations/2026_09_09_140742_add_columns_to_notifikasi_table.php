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
        Schema::table('notifikasi', function (Blueprint $table) {
            if (!Schema::hasColumn('notifikasi', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained('users')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('notifikasi', 'pesan')) {
                $table->text('pesan')->after('user_id');
            }
            if (!Schema::hasColumn('notifikasi', 'channel')) {
                $table->string('channel')->default('in_app')->after('pesan');
            }
            if (!Schema::hasColumn('notifikasi', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('channel');
            }
            if (!Schema::hasColumn('notifikasi', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'pesan', 'channel', 'is_read', 'read_at']);
        });
    }
};
