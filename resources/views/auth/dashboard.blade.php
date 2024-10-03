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


<div class="row justify-content-center mt-3" >
    <div class="col-md-8" style="width: 100%; padding: 15px;">
        <div class="card">
            <div class="card-header">Dashboard</div>
            <div class="card-body">
                <div class="alert alert-success" id="message">
                    @if ($message = Session::get('success'))
                            {{ $message }}
                    @else
                            You are logged in!
                    @endif         
                </div>
                <h2>{{ $greeting }}</h2>
                <div class="row">
                    <div class="col-md-4" style="margin-left: 42px;">
                        @if(Auth::check() && Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="img-thumbnail" style="width: 150px; height: 150px;">
                        @else
                            <p>No profile picture found. <a href="{{ route('showUploadForm') }}">Upload a picture</a></p>
                        @endif
                    </div>
                    <div class="col-md-7 d-flex flex-column justify-content-start" style="margin-top: 5px;">
                        <!-- <br> -->
                        <a href="{{ route('blog.index') }}" class="btn btn-primary mb-2">My E-Books</a>
                        <a href="{{ route('blog.create') }}" class="btn btn-success mb-2">Create Blog</a>
                        <!-- <a href="{{ route('users.index') }}" class="btn btn-warning">User Controller</a> -->
                        <a href="{{ route('blog.orders') }}" class="btn btn-warning">My Orders</a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <h2>All E-Books</h2>
                <div class="row">
                    @foreach ($blogs as $blog)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ substr($blog->name, 0, 28) . (strlen($blog->name) > 28 ? '...' : '.') }}</h5>
                                    <p class="card-text">{{ substr($blog->detail, 0, 55) . (strlen($blog->detail) > 55 ? '...' : '.') }}</p>
                                    @php
                                        $blogUser = \App\Models\User::find($blog->createdBy);
                                    @endphp
                                    @if ($blogUser)
                                        <p class="card-author">By {{ $blogUser->name }}</p>
                                    @else
                                        <p class="card-author">By Former User</p>
                                    @endif
                                    @if($user->id===$blog->createdBy)
                                        <a href="{{ route('blog.show', $blog->id) }}" class="btn btn-primary">Show Blog</a>
                                    @elseif (in_array($blog->id, $allowedBlogIds))
                                        <p>You purchased this blog!</p>
                                        <a href="{{ route('blog.show', $blog->id) }}" class="btn btn-primary">Show Blog</a>
                                    @else
                                        <a href="{{ route('blog.buy', ['userId' => $user->id, 'blogId' => $blog->id]) }}" class="btn btn-primary">Buy Blog</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
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