<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\EmailVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Jobs\DeleteUnverifiedUsersJob;
use Illuminate\Support\Facades\Artisan;


class LoginRegisterController extends Controller
{
    /**
     * Instantiate a new LoginRegisterController instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except([  //guest=>Unauthorized Users (This will Not be Allowed)
            'logout', 'dashboard','changePassStore',
        ]);
    }

    /**
     * Display a registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        // Set the default profile picture path
        $defaultProfilePicture = 'profile_pictures/default.jpg';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_picture' => $defaultProfilePicture,
            'is_admin' => false, 
            'email_verified_at' => now(),
        ]);

        //Mail Verification (#=#)
        // Mail::to($user->email)->send(new EmailVerification($user));
   
        //User Deletion from DB if Not verified within 10minutes
        DeleteUnverifiedUsersJob::dispatch()->delay(now()->addMinutes(1));
        

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); //By regenerating the session, Laravel generates a new session ID and token. This process helps to prevent attacks.
            
            return redirect()->route('dashboard')
            ->withSuccess('You have successfully Signed Up!');

            // Log::info('Inside Store => First Mail sent');
            // return redirect()->route('verification.notice')
            //        ->with('success', 'Please check your email for a verification link.');  //route('dashboard')
        } else {
            return redirect()->route('login')->with('error', 'Failed to log in after registration. Please try logging in manually.');
        }
    }

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials))
        {
            $request->session()->regenerate();
            return redirect()->route('dashboard')
                ->withSuccess('You have successfully logged in!');
        }

        return back()->withErrors([
            'email' => 'Your provided credentials do not match in our records.',
        ])->onlyInput('email');

    } 
    
    /**
     * Display a dashboard to authenticated users.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {       
        Log::info('Inside Dashboard');

        if (Auth::check()) {
            $user = Auth::user();
            $blogs = Blog::all();  
            $i=0;
            // Get allowed blog IDs for the logged-in user
            $allowedBlogIds = Order::where('user_id', $user->id)
                ->where('status', 'SUCCESS') // Assuming you have a 'status' field in your orders table
                ->pluck('blog_id')
                ->toArray();

            if ($user->is_admin == true) {        
                $orders = Order::with(['user', 'blog'])->orderBy('created_at', 'desc')->get();
                $users = User::orderBy('created_at', 'desc')->get();
                $blogs = Blog::with(['user'])->orderBy('created_at', 'desc')->get();

                $successfulTransactions = Order::where('status', 'SUCCESS')->count();
                $failedTransactions = Order::where('status', 'FAILED')->count();
                $totalUsers = $users->count();
                $totalBlogs = $blogs->count();
                
                return view('admin.dashboard', compact('orders', 'users', 'blogs', 'successfulTransactions','failedTransactions', 'totalUsers', 'totalBlogs'));
            }

            if ($user->hasVerifiedEmail()) {
                return view('auth.dashboard',compact('user','blogs', 'i','allowedBlogIds'));
            } else {
                return redirect()->route('verification.notice')
                    ->with('error', 'Please verify your email to access the dashboard.');
            }
        }
    
        return redirect()->route('login')
            ->withErrors([
                'email' => 'Please log in to access the dashboard.',
            ])->onlyInput('email');
    }
    
    
    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');;
    }    

    
    public function changePassStore(Request $request)
    {
        // Validate the form data
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Verify the current password
        if (Hash::check($request->current_password, $user->password)) {
            // Password is correct, proceed with password update
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            Log::info('Inside changePassStore');
            return redirect()->route('dashboard')->with('success', 'Password updated successfully!');
        } else {
            // Password is incorrect
            // Redirect back with an error message
            return redirect()->back()->with('password_mismatch', 'Current password does not match.');
        }
    }

    public function cusRegister()
    {
        return view('customers.register');
    }

    public function cusStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:customers',
            'password' => 'required|min:8|confirmed'
        ]);

        // Set the default profile picture path
        $defaultProfilePicture = 'profile_pictures/default.jpg';

        $user = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_picture' => $defaultProfilePicture,
            'is_admin' => false, 
        ]);



        $credentials = $request->only('email', 'password');
        if(Auth::attempt($credentials))
        {
            $request->session()->regenerate();
            return redirect()->route('dashboard')
                ->withSuccess('You have successfully logged in!');
        }
    }

    public function cusLogin()
    {
        return view('customers.login');
    }

    public function cusAuthenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Use the Customer guard for authentication
        if (Auth::guard('customer')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard') // Adjust the route as needed
                ->withSuccess('You have successfully logged in!');
        }

        return back()->withErrors([
            'email' => 'Your provided credentials do not match in our records.',
        ])->onlyInput('email');

    }
}