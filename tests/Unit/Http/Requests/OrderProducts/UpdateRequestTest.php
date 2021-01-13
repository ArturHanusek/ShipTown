<?php

namespace Tests\Unit\Http\Requests\OrderProducts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Requests\OrderProducts\UpdateRequest
 */
class UpdateRequestTest extends TestCase
{
    /** @var \App\Http\Requests\OrderProducts\UpdateRequest */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new \App\Http\Requests\OrderProducts\UpdateRequest();
    }

    /**
     * @test
     */
    public function authorize()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $actual = $this->subject->authorize();

        $this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function rules()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $actual = $this->subject->rules();

        $this->assertValidationRules([
            'quantity_shipped' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
        ], $actual);
    }

    // test cases...
}