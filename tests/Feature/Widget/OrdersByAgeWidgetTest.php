<?php

namespace Tests\Feature\Widget;

use App\Models\Order;
use App\Widgets\OrdersByAgeWidget;
use Tests\TestCase;

class OrdersByAgeWidgetTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfRuns()
    {
        factory(Order::class)->create();

        $widget = new OrdersByAgeWidget();

        $view = $widget->run();

        $this->assertNotNull($view);
    }
}
