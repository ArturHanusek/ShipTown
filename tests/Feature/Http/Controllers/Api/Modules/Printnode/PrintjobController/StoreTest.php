<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\PrintNode\PrintjobController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create()->assignRole('user');
        $this->actingAs($user, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $response = $this->postJson(route('api.modules.printnode.printjobs.store'), []);
        $response->assertStatus(422);
    }
}