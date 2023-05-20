<?php

namespace Tests\Browser;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @throws Throwable
     *
     * @return void
     */
    public function testBasicExample()
    {
        User::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Login')

                ->screenshot('LoginPage');
        });
    }
}
