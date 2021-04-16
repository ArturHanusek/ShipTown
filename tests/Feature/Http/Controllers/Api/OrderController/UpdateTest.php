<?php

namespace Tests\Feature\Http\Controllers\Api\OrderController;

use App\Models\Order;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_update_call_returns_ok()
    {
        $order = factory(Order::class)->create();
        $user = factory(User::class)->create();

        $response = $this->putJson(route('orders.update', [$order]), [
            'status_code' => "test_status_code",
            'packer_user_id' => $user->getKey(),
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ]
            ]
        ]);
    }
}
