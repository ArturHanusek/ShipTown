<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesAutomationsExecutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules_automations_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id');
            $table->smallInteger('priority')->nullable(false)->default(0);
            $table->string('execution_class')->nullable(false);
            $table->string('execution_value')->nullable(false)->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules_automations_executions');
    }
}
