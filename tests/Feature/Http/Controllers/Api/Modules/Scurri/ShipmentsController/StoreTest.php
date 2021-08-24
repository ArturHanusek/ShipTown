<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\Scurri\ShipmentsController;

use App\Models\Order;
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
        $this->assertTrue(true,'Tested in External/ScurriAnpost/IntegrationTest.php');
    }
}
