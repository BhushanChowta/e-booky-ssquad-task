<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Stripe\Stripe;
use Stripe\Checkout\Session; // Import the Session class for Stripe Checkout

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $loggedUser = Auth::user();
        $blogs = Blog::all();  
        $i=0;
        // Get allowed blog IDs for the logged-in user
        $allowedBlogIds = Order::where('user_id', $loggedUser->id)
            ->where('status', 'SUCCESS') // Assuming you have a 'status' field in your orders table
            ->pluck('blog_id')
            ->toArray();

        return view('blogs.index', compact('loggedUser','blogs', 'i','allowedBlogIds'));  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('blogs.create');  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
    
        request()->validate([
            'name' => 'required|string|max:250',
            'detail' => 'nullable|string',
        ]);
    
        // Add user_id to the request data before creating the blog
        $requestData = $request->all();
        $requestData['createdBy'] = $user->id;
    
        $blog =Blog::create($requestData);

        $user->push('blogIDs', $blog->id);

        return redirect('/dashboard/blog')->with('success', 'Blog created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog)
    {
        $loggedUser = Auth::user();
        $createdUser = \App\Models\User::find($blog->createdBy);
        return view('blogs.show',compact('blog','loggedUser','createdUser'));  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        return view('blogs.edit',compact('blog'));  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blog $blog)
    {  
        request()->validate([  
           'name' => 'required',  
           'detail' => 'required',  
       ]);  
 
       $blog->update($request->all());  
 
       return redirect()->route('blog.index')  
                       ->with('success','Blog updated successfully');  
    } 

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog)
    {  
        $user = Auth::user();

        // Remove the blog ID from the user's blogIDs array
        $user->pull('blogIDs', $blog->id);
    
        
        $blog->delete();  
  
        return redirect()->route('blog.index')  
                        ->with('success','Blog deleted successfully');  
    }  


    public function buy($userId, $blogId)
    {
        // 1. Get the authenticated user and the blog
        $user = Auth::user();
        $blog = Blog::find($blogId);

        // 2. Check if the user is trying to buy their own blog
        if ($user->id === $blog->createdBy) {
            return redirect()->route('blog.index')->with('error', 'You cannot buy your own blog.');
        }

        // 3. Set up Stripe
        Stripe::setApiKey('sk_test_51PWz1jRqTsGwpdvh7rNVecHgyKawjy2XGGuL2YHpWmx9EBnC6eiBq8OxX7eMDQR7YwvzebT6eaCqz4VzaU2wI6Jk00ncFCEYJv');

        // 4. Create a Stripe Checkout Session
        $session = Session::create([
            'mode' => 'payment',
            'success_url' => route('blog.success', ['userId' => $userId, 'blogId' => $blogId]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('blog.failed', ['userId' => $userId, 'blogId' => $blogId]) . '?session_id={CHECKOUT_SESSION_ID}', // Redirect to blog index page if payment is canceled
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd', // Set your desired currency
                        'product_data' => [
                            'name' => $blog->name,
                        ],
                        'unit_amount' => $blog->price ?? 1000, // Set the price in cents (e.g., $10.00)
                    ],
                    'quantity' => 1,
                ],
            ],
        ]);

        // 5. Redirect to Stripe Checkout
        return redirect($session->url);
    }

    public function success($userId, $blogId)
    {
        // Retrieve the session from Stripe using the session ID (you'll get this from Stripe's webhook or success redirect)
        $sessionId = request()->get('session_id'); // Assuming Stripe redirects with session_id
        $stripe = new \Stripe\StripeClient('sk_test_51PWz1jRqTsGwpdvh7rNVecHgyKawjy2XGGuL2YHpWmx9EBnC6eiBq8OxX7eMDQR7YwvzebT6eaCqz4VzaU2wI6Jk00ncFCEYJv');
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        // // Create a new order in your database
        Order::create([
            'user_id' => $userId,
            'blog_id' => $blogId,
            'status' => 'SUCCESS',
            'stripe_session_id' => $session->id,
            'amount' => $session->amount_total / 100,
        ]);

        return redirect('/dashboard')->with('success', 'Order Successful.');
    }

    public function failed($userId, $blogId)
    {
        // Retrieve the session from Stripe using the session ID (you'll get this from Stripe's webhook or success redirect)
        $sessionId = request()->get('session_id'); // Assuming Stripe redirects with session_id
        $stripe = new \Stripe\StripeClient('sk_test_51PWz1jRqTsGwpdvh7rNVecHgyKawjy2XGGuL2YHpWmx9EBnC6eiBq8OxX7eMDQR7YwvzebT6eaCqz4VzaU2wI6Jk00ncFCEYJv');
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        // // Create a new order in your database
        Order::create([
            'user_id' => $userId,
            'blog_id' => $blogId,
            'status' => 'FAILED',
            'stripe_session_id' => $session->id,
            'amount' => $session->amount_total / 100,
        ]);

        return redirect('/dashboard')->with('failed', 'Order Failed.');
    }

}
