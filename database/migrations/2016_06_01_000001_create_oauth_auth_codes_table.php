<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthAuthCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('printer_id')->nullable();
            $table->string('address_label_template')->nullable();
            $table->boolean('ask_for_shipping_number')->default(true);
            $table->string('name');
            $table->string('email')->unique();
            $table->foreignId('warehouse_id')->nullable(true);
            $table->foreignId('location_id')->nullable(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('two_factor_code')->nullable();
            $table->dateTime('two_factor_expires_at')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('oauth_auth_codes', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('client_id');
            $table->text('scopes')->nullable();
            $table->boolean('revoked');
            $table->dateTime('expires_at')->nullable();
        });

        Schema::create('oauth_access_tokens', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('client_id');
            $table->string('name')->nullable();
            $table->text('scopes')->nullable();
            $table->boolean('revoked');
            $table->timestamps();
            $table->dateTime('expires_at')->nullable();
        });

        Schema::create('oauth_refresh_tokens', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->string('access_token_id', 100)->index();
            $table->boolean('revoked');
            $table->dateTime('expires_at')->nullable();
        });

        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('secret', 100)->nullable();
            $table->string('provider')->nullable();
            $table->text('redirect');
            $table->boolean('personal_access_client');
            $table->boolean('password_client');
            $table->boolean('revoked');
            $table->timestamps();
        });

        Schema::create('oauth_personal_access_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->timestamps();
        });

        Schema::create(config('queue-monitor.table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_id')->index();
            $table->string('name')->nullable();
            $table->string('queue')->nullable();
            $table->timestamp('started_at')->nullable()->index();
            $table->string('started_at_exact')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->string('finished_at_exact')->nullable();
            $table->float('time_elapsed', 12, 6)->nullable()->index();
            $table->boolean('failed')->default(false)->index();
            $table->integer('attempt')->default(0);
            $table->integer('progress')->nullable();
            $table->longText('exception')->nullable();
            $table->text('exception_message')->nullable();
            $table->text('exception_class')->nullable();
            $table->longText('data')->nullable();
        });

        Schema::create('mail_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mailable');
            $table->string('to', 255)->nullable();
            $table->string('reply_to', 100)->nullable();
            $table->text('subject')->nullable();
            $table->longtext('html_template');
            $table->longtext('text_template')->nullable();
            $table->timestamps();
        });

        Schema::connection(config('activitylog.database_connection'))->create(config('activitylog.table_name'), function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->string('causer_type')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->index('log_name');
            $table->index(['subject_id', 'subject_type'], 'subject');
            $table->index(['causer_id', 'causer_type'], 'causer');
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('config');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 100)->default('');
            $table->decimal('price', 10)->default(0);
            $table->decimal('sale_price', 10)->default(0);
            $table->date('sale_price_start_date')->default('1899-01-01');
            $table->date('sale_price_end_date')->default('1899-01-01');
            $table->string('commodity_code')->default('');
            $table->decimal('quantity', 10)->default(0);
            $table->decimal('quantity_reserved', 10)->default(0);
            $table->decimal('quantity_available', 10)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('products_aliases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('alias')->unique();
            $table->timestamps();

            $table->foreign('product_id')
                ->on('products')
                ->references('id')
                ->onDelete('CASCADE');
        });

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->boolean('encrypted')->default(false);
            $table->string('company')->default('');
            $table->string('gender')->default('');
            $table->string('first_name')->default('');
            $table->string('first_name_encrypted')->nullable();
            $table->string('last_name')->default('');
            $table->string('last_name_encrypted')->nullable();
            $table->string('email')->default('');
            $table->string('email_encrypted')->nullable();
            $table->string('address1')->default('');
            $table->string('address2')->default('');
            $table->string('postcode')->default('');
            $table->string('city')->default('');
            $table->string('state_code')->default('');
            $table->string('state_name')->default('');
            $table->string('country_code')->default('');
            $table->string('country_name')->default('');
            $table->string('phone')->default('');
            $table->string('phone_encrypted')->nullable();
            $table->string('fax')->default('');
            $table->string('website')->default('');
            $table->string('region')->default('');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('address_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('address_id')
                ->references('id')
                ->on('order_addresses')
                ->onDelete('CASCADE');
        });

        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('location_id')->default('');
            $table->string('shelve_location')->default('');
            $table->decimal('quantity', 10)->default(0);
            $table->decimal('quantity_reserved', 10)->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('warehouse_id')
                ->on('warehouses')
                ->references('id')
                ->onDelete('SET NULL');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipping_address_id')->nullable();
            $table->string('order_number')->unique();
            $table->string('status_code')->default('');
            $table->boolean('is_active')->nullable(false)->default(0);
            $table->boolean('is_editing')->default(0);
            $table->decimal('total_products')->default(0);
            $table->decimal('total_shipping')->default(0);
            $table->decimal('total', 10)->default(0);
            $table->decimal('total_paid')->default(0);
            $table->decimal('total_discounts', 10)->default(0);
            $table->string('shipping_method_code')->default('')->nullable(true);
            $table->string('shipping_method_name')->default('')->nullable(true);
            $table->timestamp('order_placed_at')->useCurrent()->nullable();
            $table->timestamp('order_closed_at')->nullable();
            $table->integer('product_line_count')->default(0);
            $table->timestamp('picked_at')->nullable();
            $table->timestamp('packed_at')->nullable();
            $table->unsignedBigInteger('packer_user_id')->nullable();
            $table->decimal('total_quantity_ordered', 10)->default(0);
            $table->decimal('total_quantity_to_ship')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('shipping_address_id')
                ->on('order_addresses')
                ->references('id')
                ->onDelete('SET NULL');

            $table->foreign('packer_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
        });

        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('sku_ordered');
            $table->string('name_ordered');
            $table->decimal('price', 10, 3)->default(0);
            $table->decimal('quantity_ordered', 10)->default(0);
            $table->decimal('quantity_split', 10)->default(0);
            $table->decimal('quantity_shipped', 10)->default(0);
            $table->decimal('quantity_to_pick', 10)
                ->storedAs('quantity_ordered - quantity_split - quantity_picked - quantity_skipped_picking')
                ->comment('quantity_ordered - quantity_split - quantity_picked - quantity_skipped_picking');
            $table->decimal('quantity_to_ship', 10)
                ->storedAs('quantity_ordered - quantity_split - quantity_shipped')
                ->comment('quantity_ordered - quantity_split - quantity_shipped');
            $table->decimal('quantity_picked', 10)->default(0);
            $table->decimal('quantity_skipped_picking', 10)->default(0);
            $table->decimal('quantity_not_picked', 10)->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('product_id')
                ->on('products')
                ->references('id')
                ->onDelete('SET NULL');

            $table->foreign('order_id')
                ->on('orders')
                ->references('id')
                ->onDelete('cascade');
        });

        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->boolean('reserves_stock')->default(true);
            $table->boolean('order_active')->default(1);
            $table->boolean('sync_ecommerce')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->string('shipping_number');
            $table->string('carrier')->default('');
            $table->string('service')->default('');
            $table->string('tracking_url')->default('');
            $table->longText('base64_pdf_labels');
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('CASCADE');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
        });

        Schema::create('order_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('comment');
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('CASCADE');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
        });

        Schema::create('picks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('sku_ordered');
            $table->string('name_ordered');
            $table->decimal('quantity_picked', 10, 2)->default(0);
            $table->decimal('quantity_skipped_picking', 10, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->json('name');
            $table->json('slug');
            $table->string('type')->nullable();
            $table->integer('order_column')->nullable();
            $table->timestamps();
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->integer('tag_id')->unsigned();
            $table->morphs('taggable');
            $table->unique(['tag_id', 'taggable_id', 'taggable_type']);
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });

        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('location_id')->default('');
            $table->decimal('price', 10)->default(99999);
            $table->decimal('sale_price', 10)->default(99999);
            $table->date('sale_price_start_date')->default('1899-01-01');
            $table->date('sale_price_end_date')->default('1899-01-01');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('product_id')
                ->on('products')
                ->references('id')
                ->onDelete('CASCADE');
        });

        Schema::create('modules_printnode_clients', function (Blueprint $table) {
            $table->id();
            $table->string('api_key');
            $table->timestamps();
        });

        Schema::create('api2cart_product_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable();
            $table->string('api2cart_connection_id')->nullable();
            $table->string('api2cart_product_type')->nullable();
            $table->string('api2cart_product_id')->nullable();
            $table->dateTime('last_fetched_at')->nullable();
            $table->json('last_fetched_data')->nullable();
            $table->decimal('api2cart_quantity', 10, 2)->nullable();
            $table->decimal('api2cart_price', 10, 2)->nullable();
            $table->decimal('api2cart_sale_price', 10, 2)->nullable();
            $table->date('api2cart_sale_price_start_date')->nullable();
            $table->date('api2cart_sale_price_end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('module_auto_status_pickings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')
                ->nullable(false)
                ->default(true);
            $table->timestamps();
        });

        Schema::create('modules_dpd-ireland_configuration', function (Blueprint $table) {
            $table->id();
            $table->boolean('live')->nullable(false)->default(false);
            $table->string('token');
            $table->string('user');
            $table->string('password');
            $table->string('contact')->nullable(false)->default('');
            $table->string('contact_telephone')->nullable(false)->default('');
            $table->string('contact_email')->nullable(false)->default('');
            $table->string('business_name')->nullable(false)->default('');
            $table->string('address_line_1')->nullable(false)->default('');
            $table->string('address_line_2')->nullable(false)->default('');
            $table->string('address_line_3')->nullable(false)->default('');
            $table->string('address_line_4')->nullable(false)->default('');
            $table->string('country_code', 10)->nullable(false)->default('');
            $table->timestamps();
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('service_provider_class')->nullable(false);
            $table->boolean('enabled')->nullable(false)->default(false);
            $table->timestamps();
        });

        Schema::create('modules_autostatus_picking_configurations', function (Blueprint $table) {
            $table->id();
            $table->integer('max_batch_size')->nullable(false)->default(10);
            $table->integer('max_order_age')->nullable(false)->default(5);
            $table->timestamps();
        });

        Schema::create('modules_printnode_print_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // = 'Url Print';
            $table->string('printer_id'); // = $printerId;
            $table->string('content_type'); // = 'pdf_base64';
            $table->longText('content'); // = $base64PdfString;
            $table->integer('expire_after');
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('navigation_menu', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('url', 255);
            $table->string('group', 100);
            $table->timestamps();
        });

        Schema::create('modules_automations', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('priority')->nullable(false)->default(0);
            $table->boolean('enabled')->nullable(false)->default(false);
            $table->string('name')->nullable(false);
            $table->text('description')->nullable();
            $table->string('event_class')->nullable();
            $table->timestamps();
        });

        Schema::create('modules_automations_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id');
            $table->string('condition_class')->nullable();
            $table->string('condition_value')->nullable()->default('');
            $table->timestamps();
        });

        Schema::create('modules_automations_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id');
            $table->smallInteger('priority')->nullable(false)->default(0);
            $table->string('action_class')->nullable();
            $table->string('action_value')->nullable(false)->default('');
            $table->timestamps();
        });

        Schema::create('modules_automations_order_lock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('CASCADE');
        });

        Schema::create('orders_products_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('warehouse_id')->nullable();
            $table->foreignId('order_id')->nullable();
            $table->foreignId('order_product_id');
            $table->foreignId('order_shipment_id')->nullable();
            $table->string('sku_shipped')->default('');
            $table->decimal('quantity_shipped', 10);
            $table->timestamps();

            $table->foreign('order_id')
                ->on('orders')
                ->references('id')
                ->onDelete('SET NULL');
        });

        Schema::create('rmsapi_connections', function (Blueprint $table) {
            $table->id();
            $table->string('location_id');
            $table->string('url');
            $table->string('username');
            $table->string('password');
            $table->unsignedBigInteger('products_last_timestamp')->default(0);
            $table->timestamps();
        });

        Schema::create('rmsapi_product_imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('connection_id');
            $table->uuid('batch_uuid')->nullable();
            $table->dateTime('when_processed')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('sku')->nullable();
            $table->json('raw_import');
            $table->timestamps();

            $table->foreign('product_id')
                ->on('products')
                ->references('id')
                ->onDelete('SET NULL');
        });

        Schema::create('api2cart_connections', function (Blueprint $table) {
            $table->id();
            $table->string('location_id')->default('0');
            $table->string('type')->default('');
            $table->string('url')->default('');
            $table->char('prefix', 10)->default('');
            $table->string('bridge_api_key')->nullable();
            $table->unsignedBigInteger('magento_store_id')->nullable();
            $table->string('magento_warehouse_id')->nullable();
            $table->unsignedBigInteger('inventory_location_id')->nullable();
            $table->unsignedBigInteger('pricing_location_id')->nullable();
            $table->dateTime('last_synced_modified_at')->default('2020-01-01 00:00:00');
            $table->timestamps();
        });

        Schema::create('api2cart_order_imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('connection_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('shipping_method_name')->nullable(true);
            $table->string('shipping_method_code')->nullable(true);
            $table->dateTime('when_processed')->nullable();
            $table->string('order_number')->nullable();
            $table->integer('api2cart_order_id');
            $table->json('raw_import');
            $table->timestamps();

            $table->index('order_number');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('SET NULL');

            $table->foreign('connection_id')
                ->references('id')
                ->on('api2cart_connections')
                ->onDelete('SET NULL');
        });

        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->default('');
            $table->timestamps();
        });

        Schema::create('heartbeats', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('error_message', 255)->nullable();
            $table->timestamp('expires_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });

        Schema::create('modules_dpduk_connections', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->string('account_number');
            $table->foreignId('collection_address_id');
            $table->string('geo_session')->nullable();
            $table->timestamps();
        });

        Schema::create('modules_boxtop_warehouse_stock', function (Blueprint $table) {
            $table->id();
            $table->string('SKUGroup');
            $table->string('SKUNumber');
            $table->string('SKUName');
            $table->string('Attributes');
            $table->string('Warehouse');
            $table->float('WarehouseQuantity');
            $table->float('Allocated');
            $table->float('Available');
            $table->timestamps();
        });

        Schema::create('modules_boxtop_order_lock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique();
            $table->timestamps();
        });
    }
}
