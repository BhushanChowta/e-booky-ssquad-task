<?php
namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;

class VerificationController extends Controller
{
    public function verifyEmail(Request $request, $id, $hash)
    {
        // Find the user by ID
        $user = User::find($id);

        // Verify if the user exists and the hash matches
        if ($user && hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            // Mark the email as verified
            $user->markEmailAsVerified();

            if (!Auth::check()) {
                // Log in the user using the retrieved user instance
                Auth::login($user);
                $request->session()->regenerate();

                return redirect('/dashboard'); // Redirect the user to the dashboard or any other desired route
            } else {
                // Failed to log in with the provided email and password
                return redirect()->route('login')->with('error', 'Failed to log in after registration. Please try logging in manually.');
            }
        } else {
            // Invalid verification link, redirect the user to a failure page or any other desired route
            return redirect('/login-error');
        }
    }
}


