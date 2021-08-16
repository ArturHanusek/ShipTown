<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\WidgetController;

use App\Models\Widget;
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
    public function test_store_call_returns_ok()
    {
        $widget = Widget::create(['name' => 'testing', 'config' => []]);

        $response = $this->put(route('widgets.update', $widget), [
            'name'   => 'Tes widget',
            'config' => []
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'name',
                'config' => [],
                'id'
            ],
        ]);
    }
}
