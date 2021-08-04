<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\OrderStatusController;

use App\Models\OrderStatus;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private function simulationTest()
    {
        $response = $this->post(route('api.settings.order-statuses.store'), [
            'name'              => 'Test Create',
            'code'              => 'test-create',
            'order_active'      => 0,
            'reserves_stock'    => 0,
            'sync_ecommerce'    => 0,
        ]);

        return $response;
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );

        $response = $this->simulationTest();

        $response->assertSuccessful();
    }

    public function test_store_call_should_be_loggedin()
    {
        $response = $this->simulationTest();

        $response->assertRedirect(route('login'));
    }

    public function test_store_call_should_loggedin_as_admin()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->simulationTest();

        $response->assertForbidden();
    }

    public function test_all_field_is_required()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );

        $response = $this->post(route('api.settings.order-statuses.store'), [
            'name'              => '',
            'code'              => '',
            'order_active'      => '',
            'reserves_stock'    => '',
            'sync_ecommerce'    => '',
        ]);

        $response->assertSessionHasErrors([
            'name',
            'code',
            'order_active',
            'reserves_stock',
            'sync_ecommerce',
        ]);
    }

    public function test_name_and_code_cannot_duplicate()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );

        $this->simulationTest();
        $response = $this->simulationTest();

        $response->assertSessionHasErrors([
            'name',
            'code',
        ]);
    }
}
