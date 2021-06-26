<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\DpdIreland\DpdIrelandController;

use App\Modules\DpdIreland\src\Models\DpdIreland;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_index_call_with_config()
    {
        DpdIreland::query()->create([
            'live'              => false,
            'user'              => 'someuser',
            'password'          => 'somepassword',
            'token'             => 'sometoken',
            'contact'           => 'DPD Contact',
            'contact_telephone' => '0860000000',
            'contact_email'     => 'testemail@dpd.ie',
            'business_name'     => 'DPD API Test Limited',
            'address_line_1'    => 'Athlone Business Park',
            'address_line_2'    => 'Dublin Road',
            'address_line_3'    => 'Athlone',
            'address_line_4'    => 'Co. Westmeath',
            'country_code'      => 'IE',
        ]);

        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->get(route('api.settings.module.dpd-ireland.connections.index'));
        $response->assertOk();
    }
}
