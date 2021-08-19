<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Automation\AutomationController;

use App\Events\Order\OrderCreatedEvent;
use App\Modules\Automations\src\Models\Automation;
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
        $automation = Automation::create([
            'name' => 'Store Pickup',
            'priority' => 1,
            'event_class' => OrderCreatedEvent::class,
        ]);

        $data = [
            'name' => 'Test Automation',
            'event_class' => 'App\Events\Order\OrderCreatedEvent',
            'enabled' => true,
            'priority' => 1,
            'conditions' => [
                [
                    'validation_class' => 'App\Modules\Automations\src\Validators\Order\StatusCodeEqualsValidator',
                    'condition_value' => 'paid'
                ],
                [
                    'validation_class' => 'App\Modules\Automations\src\Validators\Order\ShippingMethodCodeEqualsValidator',
                    'condition_value' => 'paid'
                ]
            ],
            'executions' => [
                [
                    'priority' => 1,
                    'execution_class' => 'App\Modules\Automations\src\Executors\Order\SetStatusCodeExecutor',
                    'execution_value' => 'store_pickup',
                ]
            ]
        ];
        $response = $this->put(route('api.settings.module.automations.update', $automation->id), $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'enabled'
            ]
        ]);
    }
}