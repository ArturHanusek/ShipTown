<?php

namespace Tests\Feature\Http\Controllers\Api\ProductController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_store_call_returns_ok()
    {
        $this->markTestSkipped();
    }
}
