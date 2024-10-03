<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginRegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ProfilePictureController;
use App\Http\Controllers\OrderController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::controller(LoginRegisterController::class)->group(function() {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', 'logout')->name('logout');
    Route::post('/changePassStore', 'changePassStore')->name('changePassStore');
    //customer
    Route::get('/customer/register', 'cusRegister')->name('cusRegister');
    Route::post('/customer/store', 'cusStore')->name('cusStore');
    Route::get('/customer/login', 'cusLogin')->name('cusLogin');
    Route::post('/customer/authenticate', 'cusAuthenticate')->name('cusAuthenticate');
});

Route::get('/settings', function () {
    return view('auth.settings');
})->middleware('auth')->name('settings');

Route::get('/change-password', function () {
    return view('auth.change-password');
})->middleware('auth')->name('changePassword');

Route::controller(ProfilePictureController::class)->middleware('auth')->group(function () {
    Route::get('/upload-profile-picture', 'showUploadForm')->name('showUploadForm');
    Route::post('/upload-profile-picture', 'upload')->name('uploadProfilePicture');
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

//When User clicks the url from the mail message
// Route to verify the email using the verification URL
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])
    ->middleware(['signed'])
    ->name('verification.verify');

//To send the mail to user ()
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Verification link sent!');
})->middleware(['throttle:6,1'])->name('verification.send');


/*
|--------------------------------------------------------------------------
| Blog Model Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth'])->group(function () {
    Route::resource('/dashboard/blog', BlogController::class);
});
Route::get('/dashboard/blog-stripe/{userId}/{blogId}', [BlogController::class, 'buy'])->name('blog.buy');
Route::get('/blog/success/{userId}/{blogId}', [BlogController::class, 'success'])->name('blog.success');
Route::get('/blog/failed/{userId}/{blogId}', [BlogController::class, 'failed'])->name('blog.failed');

/*
|--------------------------------------------------------------------------
| User Controller Routes
|--------------------------------------------------------------------------
|
*/
Route::middleware(['auth'])->group(function () {
    Route::resource('/dashboard/users', UserController::class)->only(['index', 'update', 'destroy']);
});


Route::get('/customer/orders', [OrderController::class, 'index'])->name('blog.orders');
