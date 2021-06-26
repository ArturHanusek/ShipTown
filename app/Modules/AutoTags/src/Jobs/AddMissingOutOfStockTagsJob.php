<?php

namespace App\Modules\AutoTags\src\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AddMissingOutOfStockTagsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Starting RemoveWrongOutOfStockTagsJob');

        $query = Product::where('quantity_available', '<=', 0)
            ->withoutAllTags(['Out Of Stock']);

        $totalCount = $query->count();
        $chunkSize = 50;

        $query->chunk($chunkSize, function (Collection $productsCollection) use ($totalCount, $chunkSize) {
            $this->queueProgressChunk($totalCount, $chunkSize);

            $productsCollection->each(function (Product $product) {
                $product->attachTag('Out Of Stock');
            });
        });

        Log::info('Finished RemoveWrongOutOfStockTagsJob', ['records_corrected' => $query->count()]);
    }
}
