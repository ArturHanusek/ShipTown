<table class="table table-borderless">
    <thead>
    <tr>
        <th scope="col">Active Orders By Age</th>
        <th scope="col" class="text-right">{{ $total_count }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($orders_per_days_age as $days_age)
        <tr>
            <td>
                <a href="{{ route('orders', ['is_active' => true, 'sort' => 'order_placed_at', 'age_in_days' => $days_age['days_age']]) }}">
                    {{ $days_age['days_age'] }}
                </a>
            </td>
            <td class="text-right">{{ $days_age['order_count'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
