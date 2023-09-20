<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use App\Services\InventoryService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class ProcessImportedSalesRecordsJob extends UniqueJob
{
    public int $uniqueFor = 60 * 5; // 5 minutes

    public function handle()
    {
        $batch_size = 50;
        $maxRunCount = 100;

        do {
            $this->processImportedRecords($batch_size);

            Log::info('RMSAPI ProcessImportedSalesRecordsJob Processed imported sales records', [
                'count' => $batch_size,
            ]);

            $hasNoRecordsToProcess = ! RmsapiSaleImport::query()
                ->whereNull('reserved_at')
                ->whereNull('processed_at')
                ->whereNotNull('product_id')
                ->whereNotNull('warehouse_id')
                ->where('comment', 'not like', 'PM_OrderProductShipment_%')
                ->exists();

            if ($hasNoRecordsToProcess) {
                return true;
            }

            $maxRunCount--;
        } while ($maxRunCount > 0);
    }

    private function processImportedRecords(int $batch_size): void
    {
        $reservationTime = now();

        $ids = RmsapiSaleImport::query()
            ->whereNull('reserved_at')
            ->whereNull('processed_at')
            ->whereNotNull('product_id')
            ->whereNotNull('warehouse_id')
            ->where('comment', 'not like', 'PM_OrderProductShipment_%')
            ->orderBy('id')
            ->limit($batch_size)
            ->pluck('id');

        RmsapiSaleImport::query()
            ->whereIn('id', $ids)
            ->whereNull('reserved_at')
            ->update(['reserved_at' => $reservationTime]);

        // process records
        RmsapiSaleImport::query()
            ->whereIn('id', $ids)
            ->where('reserved_at', $reservationTime)
            ->whereNull('processed_at')
            ->orderBy('id')
            ->get()
            ->each(function (RmsapiSaleImport $salesRecord) {
                try {
                    retry(2, function () use ($salesRecord) {
                        $this->import($salesRecord);
                    }, 100);
                } catch (Exception $e) {
                    report($e);
                    Log::emergency($e->getMessage(), $e->getTrace());
                }
            });
    }

    private function import(RmsapiSaleImport $salesRecord)
    {
        $inventoryMovement = InventoryMovement::query()
            ->where('custom_unique_reference_id', $salesRecord->uuid)
            ->first();

        if ($inventoryMovement) {
            $salesRecord->update([
                'inventory_movement_id' => $inventoryMovement->getKey(),
                'processed_at' => now()
            ]);
            return;
        }

        $inventory = Inventory::query()
            ->where('product_id', $salesRecord->product_id)
            ->where('warehouse_id', $salesRecord->warehouse_id)
            ->first();

        ray('$salesRecord->transaction_time', $salesRecord->transaction_time);
        if ($salesRecord->type === 'rms_sale') {
            $inventoryMovement = InventoryService::sellProduct(
                $inventory,
                $salesRecord->quantity,
                $salesRecord->type,
                $salesRecord->uuid,
                Carbon::createFromTimeString($salesRecord->transaction_time)
            );
        } else {
            $inventoryMovement = InventoryService::adjustQuantity(
                $inventory,
                $salesRecord->quantity,
                $salesRecord->type,
                $salesRecord->uuid,
                Carbon::createFromTimeString($salesRecord->transaction_time)
            );
        }

        $salesRecord->update([
            'inventory_movement_id' => $inventoryMovement->getKey(),
            'processed_at' => now()
        ]);
    }
}
