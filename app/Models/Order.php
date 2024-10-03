<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Order extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'blog_id', 'status', 'stripe_session_id', 'amount','createdBy',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Assuming user_id is the foreign key
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id'); // Assuming blog_id is the foreign key
    }
}
