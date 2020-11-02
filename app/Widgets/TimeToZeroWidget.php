<?php

namespace App\Widgets;

use App\Models\Order;
use App\Models\OrderStatus;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\DB;

class TimeToZeroWidget extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $data = [];

        $startDate = DB::raw('adddate(now(), -7)');

        $data['orders_placed_count'] = Order::whereIn('status_code', OrderStatus::getActiveStatusCodesList())
            ->where('order_placed_at', '>', $startDate)
            ->count();

        $data['orders_closed_count'] = Order::whereIn('status_code', OrderStatus::getCompletedStatusCodeList())
            ->where('order_closed_at', '>', $startDate)
            ->count();

        $data['active_orders_count'] = Order::whereIn('status_code', OrderStatus::getActiveStatusCodesList())
            ->count();

        $data['balance'] = $data['orders_closed_count'] - $data['orders_placed_count'];

        $data['staff_days_used'] = (integer) DB::query()
            ->fromSub(
                Order::query()
                ->select([
                    DB::raw('Date(order_placed_at) as date'),
                    DB::raw('count(distinct(packer_user_id)) as total'),
                ])
                ->where('order_closed_at', '>', $startDate)
                ->whereNotNull('packer_user_id')
                ->groupBy([
                    'date',
                ]),
                'staff_count_daily'
            )
            ->sum('total');

        $data['periods_to_zero'] = $data['active_orders_count'] / $data['balance'];

        $data['avg_per_staff_per_day'] = $data['orders_closed_count'] / $data['staff_days_used'];

        $data['staff_days_to_zero'] = $data['active_orders_count'] / $data['avg_per_staff_per_day'];

        return view('widgets.time_to_zero_widget', [
            'config' => $this->config,
            'data' => $data
        ]);
    }
}
