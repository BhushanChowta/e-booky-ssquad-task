<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function index()
    {
        $loggedUser = Auth::user();

        if ($loggedUser->is_admin == true) {        
            $orders = Order::with(['user', 'blog'])->get();
        } else {
            $orders = Order::with(['user', 'blog'])->where('user_id', $loggedUser->id)->get();
        }

        return view('orders.index', compact('orders'));
    }
}
