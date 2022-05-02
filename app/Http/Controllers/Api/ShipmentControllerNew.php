<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShipmentStoreRequestNew;
use App\Http\Resources\Api\ShipmentResource;
use App\Models\OrderShipment;

class ShipmentControllerNew extends Controller
{
    /**ø
     * @param ShipmentStoreRequestNew $request
     * @return ShipmentResource
     */
    public function store(ShipmentStoreRequestNew $request): ShipmentResource
    {
        $shipment = new OrderShipment($request->validated());
        $shipment->user_id = $request->user()->getKey();

        return ShipmentResource::make($shipment);
    }
}
