<?php

use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Illuminate\Database\Seeder;

class RmsapiConnectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('TEST_RMSAPI_WAREHOUSE_CODE') === null) {
            return;
        }

        factory(RmsapiConnection::class)->create([
            'location_id' => env('TEST_RMSAPI_WAREHOUSE_CODE'),
            'url' => env('TEST_RMSAPI_URL'),
            'username' => env('TEST_RMSAPI_USERNAME'),
            'password' => env('TEST_RMSAPI_PASSWORD'),
        ]);
    }
}
