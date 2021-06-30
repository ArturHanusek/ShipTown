<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\ModuleConfigurationController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_index_call_returns_ok()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );

        $response = $this->get(route('api.settings.modules.index'));

        $response->assertSuccessful();
    }

    public function test_index_call_should_be_loggedin()
    {
        $response = $this->get(route('api.settings.modules.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_call_should_loggedin_as_admin()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get(route('api.settings.modules.index'));

        $response->assertForbidden();
    }
}
