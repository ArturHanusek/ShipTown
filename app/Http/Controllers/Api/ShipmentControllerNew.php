<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ShipmentStoreRequestNew;
use App\Http\Resources\Api\ShipmentResource;
use App\Models\OrderShipment;

class ShipmentControllerNew
{
    /**ø
     * @param ShipmentStoreRequestNew $request
     * @return ShipmentResource
     */
    public function store(ShipmentStoreRequestNew $request): ShipmentResource
    {
        $shipment = new OrderShipment($request->validated());
        $shipment->user_id = $request->user()->getKey();
        $shipment->save();

        return ShipmentResource::make($shipment);
    }
}
