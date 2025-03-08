<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminRoomController;
use App\Http\Controllers\admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\user\BookingController;
use App\Http\Controllers\admin\AdminBookingController;
use App\Http\Controllers\user\UserDashboardController;
use App\Http\Controllers\user\RoomUserController;
use App\Http\Controllers\CommentController;
use App\Models\Booking;

/*
|-----------------------------------------------------------------------
| API Routes
|-----------------------------------------------------------------------
| Register API routes for your application
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * 🔹 Admin Authentication Routes
 */
Route::post('admin/login', [AdminAuthController::class, 'login']);

Route::prefix('admin')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');
});

/**
 * 🔹 Admin Protected Routes (Require Authentication)
 */
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    // Rooms Management
    Route::get('/rooms', [AdminRoomController::class, 'index']);
    Route::post('/rooms', [AdminRoomController::class, 'store']);
    Route::get('/rooms/{id}', [AdminRoomController::class, 'show']);
    Route::put('/rooms/{id}', [AdminRoomController::class, 'update']);
    Route::delete('/rooms/{id}', [AdminRoomController::class, 'destroy']);

    // Bookings
    Route::get('/bookings', [AdminBookingController::class, 'index']);
    Route::get('/bookings/{id}', [AdminBookingController::class, 'show']);
    Route::patch('/bookings/{bookId}/status', [AdminBookingController::class, 'updateStatus']);
    Route::get('/user/bookings/{bookId}', [BookingController::class, 'show']);
});

// Registration & Login routes
Route::post('/register', [RegisteredUserController::class, 'register']);
Route::post('/login', [RegisteredUserController::class, 'login']);
Route::post('/logout', [RegisteredUserController::class, 'Logout'])->middleware('auth:sanctum');

// User Protected Routes
Route::middleware('auth:api')->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/user/{user_id}/dashboard', [UserDashboardController::class, 'getUserDashboardById']);
    Route::get('/user/bookings', [UserDashboardController::class, 'getUserBookings']);
    Route::get('/user/notifications', [UserDashboardController::class, 'getNotifications']);
    Route::get('/user/booking/reject-reason/{booking_id}', [UserDashboardController::class, 'getRejectReason']);
});

// Room availability
Route::get('/rooms/available', [BookingController::class, 'showAvailableRooms']);

// Booking routes
Route::get('/api/booking/{booking_id}', [BookingController::class, 'show'])->name('api.booking.show');

// Calendar & Events
Route::get('/calendar', [BookingController::class, 'calendar'])->name('calendar');
Route::get('/get-events', [BookingController::class, 'getEvents'])->name('get-events');
Route::get('/booking/events', [BookingController::class, 'getEvents'])->name('booking.events');
Route::get('/book_detail', [BookingController::class, 'detail'])->name('booking.detail');

// Comment routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/comments', [CommentController::class, 'store']);
    Route::get('/comments/{bookingId}', [CommentController::class, 'getComments']);
    Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Booking status
Route::get('/api/booking-status/{roomId}', function ($roomId) {
    $booking = Booking::where('room_id', $roomId)->first();

    if ($booking) {
        return response()->json([
            'bookstatus' => $booking->bookstatus
        ]);
    }

    return response()->json([
        'bookstatus' => 'Pending'
    ]);
});
