<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Rmsapi\RmsapiConnectionController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $params = [
            'location_id' => rand(1, 99),
            'url'         => 'https://demo.rmsapi.products.management',
            'username'    => 'demo@products.management',
            'password'    => 'secret123',
        ];

        $response = $this->post(route('api.settings.module.rmsapi.connections.store'), $params);

        $response->assertStatus(201);
    }
}
