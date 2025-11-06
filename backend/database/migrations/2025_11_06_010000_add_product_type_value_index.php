<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attribute_value_product', function (Blueprint $table) {
            // Композитный индекс для быстрых выборок selected/base по товару
            $table->index(['product_id', 'type', 'attribute_value_id'], 'av_p_product_type_value_idx');
        });
    }

    public function down(): void
    {
        Schema::table('attribute_value_product', function (Blueprint $table) {
            try { $table->dropIndex('av_p_product_type_value_idx'); } catch (\Throwable $e) {}
        });
    }
};