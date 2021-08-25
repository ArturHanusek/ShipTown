<?php

namespace Tests\Feature\Http\Controllers\Api\Admin\UserController;

use App\Models\Warehouse;
use App\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $user = factory(User::class)->create();
        $warehouse = factory(Warehouse::class)->create();

        $response = $this->put(route('users.update', $user), [
            'name'          => 'Test User',
            'email'         => 'testing@example.com',
            'role_id'       => Role::first()->id,
            'warehouse_id'  => $warehouse->id
        ]);

        $response->assertStatus(200);
    }
}
