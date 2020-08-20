<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use App\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryRoutesTest extends TestCase
{
    public function testGetRouteUnauthorized()
    {
        $response = $this->get('/api/inventory');

        $response->assertStatus(302);
    }

    public function testGetRouteAuthorized()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/inventory');

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function testIfPostRouteIsProtected()
    {
        $response = $this->post('api/inventory');

        $response->assertStatus(302);
    }

    public function testIfCantPostWithoutData()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->postJson('/api/inventory', []);

        $response->assertStatus(422);
    }

    public function testQuantityUpdate()
    {
        Event::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $inventory = factory(Inventory::class)->make();

        $product = factory(Product::class)->create();

        $update = [
            'sku' => $product->sku,
            'location_id' => 0,
            'quantity' => $inventory->quantity,
            'quantity_reserved' => $inventory->quantity_reserved
        ];

        $response = $this->postJson('/api/inventory', $update);

        $response->assertStatus(200);
    }
}
