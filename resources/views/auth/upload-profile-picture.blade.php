@extends('auth.layouts')

@section('content')

<div class="row justify-content-center mt-5">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Upload Profile Picture</div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if(Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px;">
                    @else
                        <p>No profile picture found.</p>
                    @endif
                </div>

                <form action="{{ route('uploadProfilePicture') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3 row">
                        <label for="profile_picture" class="col-md-4 col-form-label text-md-end text-start">Profile Picture</label>
                        <div class="col-md-6">
                            <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" id="profile_picture" name="profile_picture">
                            @if ($errors->has('profile_picture'))
                                <span class="text-danger">{{ $errors->first('profile_picture') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <input type="submit" class="col-md-3 offset-md-5 btn btn-primary" value="Upload">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
