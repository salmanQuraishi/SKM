<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('permissions', 'description')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('description', 255)->nullable()->after('group');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('permissions', 'description')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
