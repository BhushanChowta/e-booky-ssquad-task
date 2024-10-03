@extends('auth.layouts')

@section('content')
@php
    date_default_timezone_set('Asia/Kolkata'); // Set timezone to Indian Standard Time (IST)
    $hour = date('H'); // Get current hour in 24-hour format
    $greeting = '';

    if ($hour >= 4 && $hour < 12) {
        $greeting = 'Good Morning';
    } elseif ($hour >= 12 && $hour < 17) {
        $greeting = 'Good Afternoon';
    } elseif ($hour >= 17 && $hour < 20) {
        $greeting = 'Good Evening';
    } else {
        $greeting = 'Good Night';
    }
@endphp

<div class="row justify-content-center mt-2" >
    <div class="col-md-8" style="width: 100%; padding: 15px;">
        <h2>{{ $greeting }} Admin</h2>
        <div class="container">
            <div class="row">
                <!-- Left Column: Overview Card -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Overview</div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Count</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Successful Orders</td>
                                        <td>{{ $successfulTransactions }}</td>
                                        <td><a href="{{ route('blog.orders') }}" class="btn btn-primary">View Orders</a></td>
                                    </tr>
                                    <tr>
                                        <td>Failed Orders</td>
                                        <td>{{ $failedTransactions }}</td>
                                        <td><a href="{{ route('blog.orders') }}" class="btn btn-primary">View Orders</a></td>
                                    </tr>
                                    <tr>
                                        <td>Users</td>
                                        <td>{{ $totalUsers }}</td>
                                        <td>
                                            @if(Auth::user()->is_admin)
                                                <a href="{{ route('users.index') }}" class="btn btn-primary">Manage Users</a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Blogs</td>
                                        <td>{{ $totalBlogs }}</td>
                                        <td><a href="{{ route('blog.index') }}" class="btn btn-primary">View Blogs</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Chart -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Transaction Overview</div>
                        <div class="card-body">
                            <!-- <canvas id="transactionChart"></canvas> -->
                            <canvas id="transactionChart" style="max-width: 500px; max-height: 280px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">Latest Orders</div>
            <div class="card-body">
                <div class="container">
                    <table class="table">
                        <thead>
                            <tr>
                                <!-- <th>Order ID</th> -->
                                <th>Customer Name</th>
                                <th>Blog Name</th>
                                <th>Stripe Invoice ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <!-- <td>{{ $order->id }}</td> -->
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

        <div class="card mt-4">
            <div class="card-header">Latest Blogs</div>
            <div class="card-body">
                <div class="container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Blog Name</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($blogs as $blog)
                                <tr>
                                    <td>{{ $blog->name }}</td>
                                    <td>{{ $blog->user->name }}</td>
                                    <td>{{ $blog->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No blogs found.</td>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
        // Automatically remove the message after 5 seconds of page load
        $(document).ready(function() {
            setTimeout(function() {
                $('#message').fadeOut('fast', function() {
                    $(this).remove(); // Remove the message element from the DOM
                });
            }, 1500);
        });
</script>
<!-- Chart.js Initialization -->
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Transaction data from your backend
        const successfulOrders = {{ $successfulTransactions }};
        const failedOrders = {{ $failedTransactions }};

        // Setup chart data
        const data = {
            labels: ['Successful Orders', 'Failed Orders'],
            datasets: [{
                data: [successfulOrders, failedOrders],
                backgroundColor: ['#4CAF50', '#FF6384'],
            }]
        };

        // Configuring the chart
        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        };

        // Rendering the pie chart in the canvas
        const ctx = document.getElementById('transactionChart').getContext('2d');
        new Chart(ctx, config);
    });
</script>