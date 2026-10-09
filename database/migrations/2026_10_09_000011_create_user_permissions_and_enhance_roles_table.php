<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'is_admin')) {
                $table->boolean('is_admin')->default(true)->after('description');
            }
            if (!Schema::hasColumn('roles', 'is_system')) {
                $table->boolean('is_system')->default(false)->after('is_admin');
            }
        });

        if (!Schema::hasTable('user_permission')) {
            Schema::create('user_permission', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
                $table->unique(['user_id', 'permission_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permission');

        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'is_system')) {
                $table->dropColumn('is_system');
            }
            if (Schema::hasColumn('roles', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
        });
    }
};
