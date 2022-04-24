<?php

namespace App\Modules\Maintenance\src\Jobs;

use App\Models\Inventory;
use App\Models\Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateInventoryWarehouseCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Warehouse $warehouse;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Inventory::query()
            ->where(['warehouse_id' => $this->warehouse->getKey()])
            ->update([
                'warehouse_code' => $this->warehouse->code,
                'location_id' => $this->warehouse->code
            ]);
    }
}
