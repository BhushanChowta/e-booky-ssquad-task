@extends('auth.layouts')

@section('content')


<div class="row justify-content-center mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Account Settings</div>

                <div class="card-body">

                    <div class="row">
                        <div class="col-md-4" style="margin-left: 42px;">
                            @if(Auth::check() && Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="img-thumbnail" style="width: 150px; height: 150px;">
                            @else
                                <p>No profile picture found. <a href="{{ route('showUploadForm') }}">Upload a picture</a></p>
                            @endif
                        </div>
                        <div class="col-md-7 d-flex flex-column justify-content-start" style="margin-top: 5px;">
                            <br>
                            <h2>{{ Auth::user()->name }}</h2>
                            <h5>{{ Auth::user()->email }}</h5>
                            @if(Auth::check() && Auth::user()->is_admin)
                                <h6>Admin User</h6>
                            @else
                                <h6>Normal User</h6>
                            @endif
                        </div>
                    </div>
                    <br>

                    <li class="list-group-item">
                        <a href="{{ route('showUploadForm') }}" class="btn btn-primary mb-2" style="margin-left: 130px;">
                            Update Profile Pic
                        </a>
                        <a href="{{ route('changePassword') }}" class="btn btn-primary mb-2" style="margin-left: 100px;">
                            Change Password
                        </a>
                    </li>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
