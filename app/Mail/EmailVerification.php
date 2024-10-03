<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User; // Import the User model
use Illuminate\Support\Facades\URL;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $user; // Declare a public property to hold the user instance

    /**
     * Create a new message instance.
     *
     * @param User $user The user instance
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user; // Store the user instance in the class property
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $verificationUrl = $this->verificationUrl();

        return $this->markdown('emails.verification', compact('verificationUrl'));
    }

    /**
     * Get the email verification URL for the user.
     *
     * @return string
     */
    protected function verificationUrl()
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60), // The URL will expire after 60 minutes
            [
                'id' => $this->user->_id,
                'hash' => sha1($this->user->getEmailForVerification()),
            ]
        );
    }
}
