<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('model_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('model_variants', 'image')) {
                $table->string('image')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('model_variants', 'status')) {
                $table->boolean('status')->default(true)->after('image');
            }
            if (!Schema::hasColumn('model_variants', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('status');
            }
            if (!Schema::hasColumn('model_variants', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'model_variant_id')) {
                $table->foreignId('model_variant_id')
                      ->nullable()
                      ->after('car_model_id')
                      ->constrained('model_variants')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'model_variant_id')) {
                $table->dropConstrainedForeignId('model_variant_id');
            }
        });

        Schema::table('model_variants', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('model_variants', 'deleted_at')) {
                $columnsToDrop[] = 'deleted_at';
            }
            if (Schema::hasColumn('model_variants', 'sort_order')) {
                $columnsToDrop[] = 'sort_order';
            }
            if (Schema::hasColumn('model_variants', 'status')) {
                $columnsToDrop[] = 'status';
            }
            if (Schema::hasColumn('model_variants', 'image')) {
                $columnsToDrop[] = 'image';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
