<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\ConfigurationController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_index_call_returns_ok()
    {
        $response = $this->get(route('api.settings.configurations.index'));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'business_name'
            ],
        ]);
    }
}
