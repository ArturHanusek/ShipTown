<?php

namespace Tests\Feature\Routes\Web\Pdf\Orders\Order_number;

use App\Models\Order;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 *
 */
class TemplateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected string $uri = 'set in setUp() method';

    /**
     * @var User
     */
    protected User $user;

    /**
     * @var Order
     */
    protected Order $order;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->order = factory(Order::class)->create();

        $this->uri = '/pdf/orders/'.$this->order->order_number. '/address_label';
    }

    /** @test */
    public function test_if_uri_set()
    {
        $this->assertNotEmpty($this->uri);
    }

    /** @test */
    public function test_guest_call()
    {
        $response = $this->get($this->uri);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_user_call()
    {
        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }

    /** @test */
    public function test_admin_call()
    {
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }
}