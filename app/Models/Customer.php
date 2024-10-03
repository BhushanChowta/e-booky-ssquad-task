<?php

namespace App\Models;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $connection = 'mongodb'; // Optional: If you have multiple connections in your database.php config
    protected $collection = 'customers'; // The name of the MongoDB collection

    // Add other properties and methods as needed
    protected $fillable = [
        'name', 'email', 'password', 'profile_picture', 'is_admin', // Add 'is_admin' to the fillable attributes
    ];

    protected $casts = [
        'is_admin' => 'boolean', // Cast 'is_admin' attribute to boolean
    ];
    
    public function getEmailForVerification()
    {
        return $this->email;
    }

    public function blogs()
    {
        return $this->belongsToMany(Blog::class);
    }

    /**
     * Check if the Customer is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Set the Customer as an admin.
     *
     * @return void
     */
    public function setAdmin()
    {
        $this->update(['is_admin' => true]);
    }

    /**
     * Revoke the admin role from the Customer.
     *
     * @return void
     */
    public function revokeAdmin()
    {
        $this->update(['is_admin' => false]);
    }

}
