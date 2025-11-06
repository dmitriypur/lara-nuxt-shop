<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attribute_value_product_variant', function (Blueprint $table) {
            // Добавляем коротко названный уникальный индекс
            $table->unique(['product_variant_id', 'attribute_value_id'], 'av_pv_unique');
        });
    }

    public function down(): void
    {
        Schema::table('attribute_value_product_variant', function (Blueprint $table) {
            $table->dropUnique('av_pv_unique');
        });
    }
};