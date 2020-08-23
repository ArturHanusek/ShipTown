<?php

namespace Tests\External\Rmsapi;

use App\Modules\Rmsapi\src\Jobs\FetchUpdatedProductsJob;
use App\Jobs\Rmsapi\ProcessImportedProductsJob;
use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportProductsJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @throws \Exception
     */
    public function testIfJobRuns()
    {
        Bus::fake();
        Event::fake();

        // we want clean data
        RmsapiConnection::query()->delete();
        RmsapiProductImport::query()->delete();

        $connection = factory(RmsapiConnection::class)->create();

        $job = new FetchUpdatedProductsJob($connection->id);

        $job->handle();

        $this->assertTrue(RmsapiProductImport::query()->exists(), 'No imports have been made');
        Bus::assertDispatched(ProcessImportedProductsJob::class);
    }
}
