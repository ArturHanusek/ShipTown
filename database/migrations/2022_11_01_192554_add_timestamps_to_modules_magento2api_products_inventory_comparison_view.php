<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToModulesMagento2apiProductsInventoryComparisonView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE VIEW modules_magento2api_products_inventory_comparison_view AS
            SELECT modules_magento2api_products.id AS modules_magento2api_products_id,
                   products.sku AS sku,
                   floor(max(modules_magento2api_products.quantity)) AS magento_quantity,
                   if((floor(sum(inventory.quantity_available)) < 0), 0, floor(sum(inventory.quantity_available))) AS expected_quantity,
                   modules_magento2api_products.stock_items_fetched_at

            from modules_magento2api_products

            left join modules_magento2api_connections
              ON modules_magento2api_connections.id = modules_magento2api_products.connection_id

            left join taggables
              ON taggables.tag_id = modules_magento2api_connections.inventory_source_warehouse_tag_id
              AND taggables.taggable_type = 'App\\\\Models\\\\Warehouse'

            left join warehouses
              ON warehouses.id = taggables.taggable_id

            left join inventory
              on inventory.product_id = modules_magento2api_products.product_id
              and inventory.warehouse_id = warehouses.id

            left join products
              on products.id = modules_magento2api_products.product_id

            group by modules_magento2api_products.id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules_magento2api_products_inventory_comparison_view', function (Blueprint $table) {
            //
        });
    }
}
