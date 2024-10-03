@extends('auth.layouts')

@section('content')

<div class="row justify-content-center mt-5">
    <div class="col-md-8">

        <div class="card">
            <div class="card-header">Change Password</div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        {{ $message }}
                    </div>
                @endif

                <!-- Display an alert message if the password does not match -->
                @if ($message = Session::get('password_mismatch'))
                    <div class="alert alert-danger">
                        {{ $message }}
                    </div>
                @endif

                <form action="{{ route('changePassStore') }}" method="post">
                    @csrf
                    <div class="mb-3 row">
                        <label for="email" class="col-md-4 col-form-label text-md-end text-start">Email</label>
                        <div class="col-md-6">
                            <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" readonly>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="current_password" class="col-md-4 col-form-label text-md-end text-start">Current Password</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                    </div>


                    <div class="mb-3 row">
                        <label for="new_password" class="col-md-4 col-form-label text-md-end text-start">New Password</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
                            @error('new_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="new_password_confirmation" class="col-md-4 col-form-label text-md-end text-start">Confirm New Password</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>    
</div>

@endsection
