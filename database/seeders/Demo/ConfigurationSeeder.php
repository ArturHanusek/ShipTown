<?php

namespace Database\Seeders\Demo;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Configuration::updateOrCreate([], [
            'business_name' => 'Demo',
            'disable_2fa' => env('DISABLE_2FA', false)
        ]);
    }
}
