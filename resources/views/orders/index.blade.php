@extends('auth.layouts')

@section('content')
<br>
<div class="row justify-content-center mt-5">
    <div class="col-md-8" style="width: 100%; padding: 15px;">
        <div class="card">
            <div class="card-header">Orders</div>
            <div class="card-body">
                <div class="container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User ID</th>
                                <th>Blog ID</th>
                                <th>Stripe Invoice ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->blog->name ?? null}}</td>
                                    <td>{{ substr($order->stripe_session_id, 0, 4) . '...' . substr($order->stripe_session_id, -7) }}</td> 
                                    <td>${{ $order->amount }}</td>
                                    <td>{{ $order->status }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
