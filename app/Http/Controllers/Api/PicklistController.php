<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Picklist\StoreRequest;
use App\Http\Resources\PicklistResource;
use App\Models\Picklist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PicklistController extends Controller
{
    public function index(Request $request)
    {
        $inventory_location_id = 100;

        $query = Picklist::query()
            ->select([
                'picklists.*',
                'pick_location_inventory.shelve_location'
            ])
            ->leftJoin('inventory as pick_location_inventory', function ($join) use ($inventory_location_id) {
                $join->on('pick_location_inventory.product_id', '=', 'picklists.product_id');
                $join->on('pick_location_inventory.location_id', '=', DB::raw($inventory_location_id));
            })
            ->with('product')
            ->with(['inventory' => function(HasMany $query) use ($inventory_location_id){
                $query->where('location_id', '=', $inventory_location_id);
                }])
            ->with('order')
            ->whereNull('picked_at')
            ->when($request->has('currentLocation') && ( ! empty($request->get('currentLocation'))),
                function ($query) use ($request) {
                    return $query->where('pick_location_inventory.shelve_location', '>=', $request->get('currentLocation'));
                })

            ->when($request->get('single_line_orders_only', false), function ($query) {
                return $query->whereHas('order', function ($query) {
                    return $query->where('product_line_count', '=', 1);
                });
            })

            ->orderBy('pick_location_inventory.shelve_location')
            ->orderBy('picklists.sku_ordered');

        return $query->paginate(3);
    }

    public function store(StoreRequest $request, Picklist $picklist)
    {
        $picklist->update([
            'picker_user_id' => $request->user()->id,
            'quantity_picked' => $picklist->quantity_picked + $request->input('quantity_picked'),
            'picked_at' => $request->input('quantity_picked') > 0 ? now() : null, // On undo, set picked_at to null
        ]);

        return new PicklistResource($picklist);
    }
}
