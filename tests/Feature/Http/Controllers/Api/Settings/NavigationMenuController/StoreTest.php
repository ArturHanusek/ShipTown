<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\NavigationMenuController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private function simulationTest($body = null)
    {
        if (is_null($body)) {
            $body = [
                'name'  => 'testing',
                'url'   => 'testing',
                'group' => 'picklist',
            ];
        }

        $response = $this->post(route('api.settings.navigation-menu.store'), $body);

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

        $response = $this->simulationTest([]);

        $response->assertSessionHasErrors([
            'name',
            'url',
            'group',
        ]);
    }

    public function test_group_not_packlist_or_picklist()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );

        $response = $this->simulationTest([
            'name'  => 'tes',
            'url'   => 'tes',
            'group' => 'tes',
        ]);

        $response->assertSessionHasErrors([
            'group',
        ]);
    }
}
