<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Добавляем колонку type и новый уникальный индекс в attribute_value_product
        Schema::table('attribute_value_product', function (Blueprint $table) {
            if (!Schema::hasColumn('attribute_value_product', 'type')) {
                $table->string('type', 16)->default('base')->after('attribute_value_id');
            }
            // Добавим отдельные индексы для FK, чтобы можно было снять старый составной уникальный индекс
            $table->index('product_id', 'av_p_product_id_idx');
            $table->index('attribute_value_id', 'av_p_attribute_value_id_idx');
        });

        // Меняем уникальные ограничения: (product_id, attribute_value_id, type)
        Schema::table('attribute_value_product', function (Blueprint $table) {
            // Сбрасываем старый индекс, если он существует
            try { $table->dropUnique('av_p_unique'); } catch (\Throwable $e) {}
            $table->unique(['product_id', 'attribute_value_id', 'type'], 'av_p_type_unique');
        });

        // 2) Переносим данные из attribute_value_product_variant -> attribute_value_product (type = 'selected')
        if (Schema::hasTable('attribute_value_product_variant')) {
            DB::table('attribute_value_product_variant')
                ->orderBy('id')
                ->chunk(500, function ($rows) {
                    $toInsert = [];
                    foreach ($rows as $row) {
                        $toInsert[] = [
                            'product_id' => $row->product_variant_id,
                            'attribute_value_id' => $row->attribute_value_id,
                            'type' => 'selected',
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ];
                    }
                    // Вставляем пакетно, игнорируя дубликаты
                    if (!empty($toInsert)) {
                        DB::table('attribute_value_product')->insertOrIgnore($toInsert);
                    }
                });

            // 3) Удаляем таблицу вариантов
            Schema::dropIfExists('attribute_value_product_variant');
        }
    }

    public function down(): void
    {
        // 1) Восстанавливаем таблицу attribute_value_product_variant
        if (!Schema::hasTable('attribute_value_product_variant')) {
            Schema::create('attribute_value_product_variant', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_variant_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();
                $table->unique(['product_variant_id', 'attribute_value_id'], 'av_pv_unique');
                $table->timestamps();
            });
        }

        // 2) Возвращаем данные type='selected' обратно в variant-пивот
        DB::table('attribute_value_product')
            ->where('type', 'selected')
            ->orderBy('id')
            ->chunk(500, function ($rows) {
                $toInsert = [];
                foreach ($rows as $row) {
                    $toInsert[] = [
                        'product_variant_id' => $row->product_id,
                        'attribute_value_id' => $row->attribute_value_id,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ];
                }
                if (!empty($toInsert)) {
                    DB::table('attribute_value_product_variant')->insertOrIgnore($toInsert);
                }
            });

        // 3) Удаляем колонку type и возвращаем старый уникальный индекс
        Schema::table('attribute_value_product', function (Blueprint $table) {
            try { $table->dropUnique('av_p_type_unique'); } catch (\Throwable $e) {}
            try { $table->dropColumn('type'); } catch (\Throwable $e) {}
            $table->unique(['product_id', 'attribute_value_id'], 'av_p_unique');
        });
    }
};