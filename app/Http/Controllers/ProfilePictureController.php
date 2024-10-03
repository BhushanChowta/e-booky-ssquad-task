<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilePictureController extends Controller
{
    public function showUploadForm()
    {
        return view('auth.upload-profile-picture');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the allowed image types and size as per your requirements
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $user = Auth::user();
            $oldProfilePicture = $user->profile_picture;

            if ($oldProfilePicture && $oldProfilePicture !== 'profile_pictures/default.jpg') {
                Storage::disk('public')->delete($oldProfilePicture);
            }

            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->update(['profile_picture' => $profilePicturePath]);
        }

        return redirect()->route('dashboard')->withSuccess('Profile picture updated successfully!');
    }
}
