<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\AutoStatus\ConfigurationController;

use App\Models\AutoStatusPickingConfiguration;
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
        $configuration = factory(AutoStatusPickingConfiguration::class)->make();

        $response = $this->postJson(route('modules.autostatus.picking.configuration.store'), $configuration->toArray());

        $response->assertSuccessful();
    }
}
