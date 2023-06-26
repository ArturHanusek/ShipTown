<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        CREATE OR REPLACE VIEW view_key_dates AS
        SELECT
          CURDATE() as date,
          DATE_ADD(CURDATE(), INTERVAL - WEEKDAY(now()) DAY) as this_week_start_date,
          DATE_ADD(CURDATE(), INTERVAL - DAY(now()) + 1 DAY) as this_month_start_date,
          DATE_ADD(CURDATE(), INTERVAL - DAYOFYEAR(now()) + 1 DAY) as this_year_start_date,

          now() as now;
        ");
    }
};
