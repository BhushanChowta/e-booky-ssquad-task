<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        // Only admin users can update roles
        if (!Gate::allows('isAdmin')) {
            return redirect()->route('users.index')->with('error', 'You do not have permission to perform this action.');
        }

        // Prevent users from setting themselves as admin
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot set yourself as an admin.');
        }

        $user->update(['is_admin' => !$user->is_admin]);

        return redirect()->route('users.index')->with('success', 'User role updated successfully.');
    }

    public function destroy(User $user)
    {
        // Only admin users can delete users
        if (!Gate::allows('isAdmin')) {
            return redirect()->route('users.index')->with('error', 'You do not have permission to perform this action.');
        }

        // Prevent users from deleting themselves
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
