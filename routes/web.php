<?php

declare(strict_types=1);

use App\Http\Controllers\AdventureController;
use App\Http\Controllers\Admin\AdventureController as AdminAdventureController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DarshanController as AdminDarshanController;
use App\Http\Controllers\Admin\DestinationController as AdminDestinationController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\HotelController as AdminHotelController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PoojaController as AdminPoojaController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DarshanController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PoojaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes
|--------------------------------------------------------------------------
|
| Guest-only routes sit behind the `guest` guard so a signed-in visitor is
| bounced to their dashboard instead of seeing a login form again.
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Catalogue
|--------------------------------------------------------------------------
*/

Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/adventures', [AdventureController::class, 'index'])->name('adventures.index');
Route::get('/darshan', [DarshanController::class, 'index'])->name('darshan.index');
Route::get('/pooja', [PoojaController::class, 'index'])->name('pooja.index');
Route::get('/food', [FoodController::class, 'index'])->name('food.index');
Route::get('/trips', [TripController::class, 'index'])->name('trips.index');

/*
|--------------------------------------------------------------------------
| Catalogue detail pages
|--------------------------------------------------------------------------
*/

Route::get('/destinations/{slug}', [DestinationController::class, 'show'])->name('destinations.show');
Route::get('/hotels/{slug}', [HotelController::class, 'show'])->name('hotels.show');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');
Route::get('/adventures/{slug}', [AdventureController::class, 'show'])->name('adventures.show');
Route::get('/darshan/{slug}', [DarshanController::class, 'show'])->name('darshan.show');
Route::get('/pooja/{slug}', [PoojaController::class, 'show'])->name('pooja.show');
Route::get('/food/{slug}', [FoodController::class, 'show'])->name('food.show');

/*
|--------------------------------------------------------------------------
| Enquiries
|--------------------------------------------------------------------------
*/

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.store');

Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('newsletter.store');

/*
|--------------------------------------------------------------------------
| Signed-in customer area
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');

    Route::get('/book/{type}/{slug}', [BookingController::class, 'create'])
        ->whereIn('type', ['hotel', 'event', 'adventure', 'darshan', 'pooja', 'restaurant', 'food'])
        ->name('bookings.create');
    Route::post('/book/{type}/{slug}', [BookingController::class, 'store'])
        ->whereIn('type', ['hotel', 'event', 'adventure', 'darshan', 'pooja', 'restaurant', 'food'])
        ->middleware('throttle:20,1')
        ->name('bookings.store');

    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/invoice', [BookingController::class, 'invoice'])->name('bookings.invoice');
    Route::get('/bookings/{booking}/pay', [BookingController::class, 'pay'])->name('bookings.pay');
    Route::post('/bookings/{booking}/pay', [BookingController::class, 'initiatePayment'])
        ->middleware('throttle:10,1')
        ->name('bookings.pay.initiate');
    Route::match(['get', 'post'], '/bookings/{booking}/callback', [BookingController::class, 'callback'])
        ->middleware('throttle:20,1')
        ->name('payments.callback');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('wishlist.store');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])
        ->name('wishlist.destroy');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/profile/preferences', [ProfileController::class, 'preferences'])->name('profile.preferences');
    Route::put('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences.update');
});

/*
|--------------------------------------------------------------------------
| Back office — gated by EnsureUserIsAdmin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'show'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Categories
        Route::resource('categories', AdminCategoryController::class)->except(['show']);

        // Destinations
        Route::resource('destinations', AdminDestinationController::class)->except(['show']);

        // Hotels
        Route::resource('hotels', AdminHotelController::class)->except(['show']);

        // Events
        Route::resource('events', AdminEventController::class)->except(['show']);

        // Adventures
        Route::resource('adventures', AdminAdventureController::class)->except(['show']);

        // VIP Darshan
        Route::resource('darshan', AdminDarshanController::class)->except(['show']);

        // Pooja Vidhi
        Route::resource('pooja', AdminPoojaController::class)->except(['show']);

        // Bookings
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/record-payment', [AdminBookingController::class, 'recordPayment'])->name('bookings.record-payment');
        Route::put('/bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
        Route::delete('/bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');

        // Payments
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/mark-paid', [AdminPaymentController::class, 'markPaid'])->name('payments.mark-paid');

        // Contact inbox
        Route::get('/contact-messages', [AdminContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('/contact-messages/{message}', [AdminContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::put('/contact-messages/{message}', [AdminContactMessageController::class, 'update'])->name('contact-messages.update');
        Route::post('/contact-messages/{message}/reply', [AdminContactMessageController::class, 'reply'])->name('contact-messages.reply');
        Route::delete('/contact-messages/{message}', [AdminContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
    });

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.attempt');

    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])
        ->middleware('throttle:register')
        ->name('register.attempt');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Signed-in account routes
|--------------------------------------------------------------------------
*/

// `verified` must NOT be applied here: EnsureEmailIsVerified redirects
// unverified users to this exact route, so guarding it with itself loops.
Route::middleware('auth')->group(function () {
    Route::get('email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');
    Route::post('email/verify/resend', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:3,1')
        ->name('verification.send');
});

Route::middleware('auth')->group(function () {
    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('logout', [LoginController::class, 'destroy'])
        ->middleware('throttle:10,1')
        ->name('logout');
});
