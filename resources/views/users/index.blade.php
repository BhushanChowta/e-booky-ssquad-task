@extends('auth.layouts')

@section('content')
<br>
<div class="row justify-content-center mt-5" >
    <div class="col-md-8" style="width: 100%; padding: 15px;">
        <div class="card">
            <div class="card-header">User Management</div>
                <div class="card-body">
                <div class="container">
                    @if(session('success'))
                        <div class="alert alert-success" id="message">{{ session('success') }}</div>
                    @elseif(session('error'))
                        <div class="alert alert-danger" id="message">{{ session('error') }}</div>
                    @endif
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                @if(Auth::user()->is_admin )
                                <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            @if($user->id !== Auth::id()) <!-- Check if the user is not the logged-in user -->
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" class="profile-picture" style="width: 30px; height: 30px;">
                                        {{ $user->name }}
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->is_admin ? 'Admin' : 'Normal' }}</td>
                                    <td>
                                        @if(Gate::allows('isAdmin') && Auth::user()->is_admin && $user->id !== Auth::id()) <!-- Check if the logged-in user is an Admin and not deleting themselves -->
                                            <form action="{{ route('users.update', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="is_admin" value="{{ !$user->is_admin }}">
                                                <button type="submit" class="btn btn-link">{{ $user->is_admin ? 'Revoke Admin' : 'Set Admin' }}</button>
                                            </form>
                                        @endif
                                        
                                        @if(Gate::allows('isAdmin') && Auth::user()->is_admin && $user->id !== Auth::id()) <!-- Allow Admin to delete both Admin and Normal users, but not themselves -->
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete the user {{$user->name}}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
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