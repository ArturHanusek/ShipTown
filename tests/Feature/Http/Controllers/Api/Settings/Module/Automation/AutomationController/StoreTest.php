<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Automation\AutomationController;

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
        $data = [
            'name' => 'Test Automation',
            'event_class' => 'App\Events\Order\OrderCreatedEvent',
            'enabled' => true,
            'priority' => 1,
            'conditions' => [
                [
                    'condition_class' => 'App\Modules\Automations\src\Conditions\Order\CanBeFulfilledCondition',
                    'condition_value' => 'paid'
                ],
                [
                    'condition_class' => 'App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsCondition',
                    'condition_value' => 'paid'
                ]
            ],
            'actions' => [
                [
                    'priority' => 1,
                    'action_class' => 'App\Modules\Automations\src\Actions\Order\SetStatusCodeAction',
                    'action_value' => 'store_pickup',
                ]
            ]
        ];
        $response = $this->post(route('api.settings.module.automations.store'), $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'enabled'
            ]
        ]);
    }
}
