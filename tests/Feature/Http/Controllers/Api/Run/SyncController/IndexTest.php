<?php

namespace Tests\Feature\Http\Controllers\Api\Run\SyncController;

use App\Events\SyncRequestedEvent;
use App\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class IndexTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('user');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_index_call_returns_ok()
    {
        Event::fake();

        $response = $this->get('api/run/sync');

        $response->assertSuccessful();

        Event::assertDispatched(SyncRequestedEvent::class);
    }
}
