<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuantityRequiredColumnOnInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->decimal('quantity_required', 10)
                ->storedAs('CASE WHEN (quantity - quantity_reserved) < reorder_point ' .
                    'THEN restock_level - (quantity - quantity_reserved) ' .
                    'ELSE 0 END')
                ->comment('CASE WHEN (quantity - quantity_reserved) < reorder_point ' .
                    'THEN restock_level - (quantity - quantity_reserved) ' .
                    'ELSE 0 END')
                ->after('restock_level');
        });
    }
}
