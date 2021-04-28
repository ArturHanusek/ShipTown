<?php

namespace Tests\Feature\Http\Controllers\Api\OrderController;

use App\User;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
            'order_number'      => '0123456789',
            'products' => [
                [
                    'sku' => '123',
                    'name' => 'Test Product',
                    'quantity'     => 2,
                    'price'        => 4,
                ],

                [
                    'sku' => '456',
                    'name' => 'Test Product',
                    'quantity'     => 5,
                    'price'        => 10,
                ],
            ],
        ];

        $response = $this->postJson('api/orders', $data);
//        dd($response->getContent());
        $response->assertStatus(200, );

        $this->assertDatabaseHas('orders', ['order_number' => $data['order_number']]);
    }

    public function testCorrectProductsSections()
    {
        $data = [
            'order_number'      => '001241',
            'products' => [
                [], // blank products record
            ],
        ];

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->postJson( 'api/orders', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.sku'])
            ->assertJsonValidationErrors(['products.0.quantity'])
            ->assertJsonValidationErrors(['products.0.price']);
    }

    public function testIfMissingOrderNumberIsNotAllowed()
    {
        $data = [
            //'order_number'      => '001241',
            'products' => [
                [
                    'sku' => '123',
                    'quantity'     => 2,
                    'price'        => 4,
                ],

                [
                    'sku' => '456',
                    'quantity'     => 5,
                    'price'        => 10,
                ],
            ],
        ];

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', 'api/orders', $data)
            ->assertStatus(422);
    }

    public function testIfMissingProductsSectionIsNotAllowed()
    {
        $data = [
            'order_number'      => '001241',
        ];

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', 'api/orders', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['products']);
    }
}
