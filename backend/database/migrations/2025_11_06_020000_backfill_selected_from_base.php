<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Заполняем selected из base для тех товаров, где selected отсутствует.
        // MySQL/InnoDB.
        DB::statement(<<<SQL
            INSERT IGNORE INTO attribute_value_product (product_id, attribute_value_id, type)
            SELECT avp_base.product_id, avp_base.attribute_value_id, 'selected'
            FROM attribute_value_product avp_base
            WHERE avp_base.type = 'base'
              AND NOT EXISTS (
                SELECT 1
                FROM attribute_value_product avp_sel
                WHERE avp_sel.product_id = avp_base.product_id
                  AND avp_sel.attribute_value_id = avp_base.attribute_value_id
                  AND avp_sel.type = 'selected'
              )
        SQL);
    }

    public function down(): void
    {
        // Откат: удаляем те selected, у которых есть соответствующая base-запись.
        DB::statement(<<<SQL
            DELETE avp_sel
            FROM attribute_value_product avp_sel
            INNER JOIN attribute_value_product avp_base
              ON avp_base.product_id = avp_sel.product_id
             AND avp_base.attribute_value_id = avp_sel.attribute_value_id
             AND avp_base.type = 'base'
            WHERE avp_sel.type = 'selected'
        SQL);
    }
};