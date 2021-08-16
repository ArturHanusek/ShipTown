<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Api2cart\Api2cartConnectionController;

use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_destroy_call_returns_ok()
    {
        $api2cart = Api2cartConnection::create([
            'location_id'   => '99',
            'type'          => 'tes',
            'url'           => 'tes',
            'prefix'        => 'tes'
        ]);
        $response = $this->delete(route('api.settings.module.api2cart.connections.destroy', $api2cart));
        $response->assertStatus(200);
    }
}
