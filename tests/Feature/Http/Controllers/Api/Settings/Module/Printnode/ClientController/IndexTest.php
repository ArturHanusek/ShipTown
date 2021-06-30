<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Printnode\ClientController;

use App\Modules\PrintNode\src\Models\Client;
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
        factory(Client::class)->create();

        $response = $this->get(route('api.settings.module.printnode.clients.index'));

        $response->assertSuccessful();
    }
}
