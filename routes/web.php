<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\User\RoomUserController;
use App\Http\Controllers\RoomDetailController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\User\UserDashboardController;

/*
|---------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('user.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleCallback']);

Route::get('/rooms', [RoomUserController::class, 'index'])->middleware('auth')->name('rooms.index');
Route::get('/rooms/{room_id}', [RoomDetailController::class, 'show']);
Route::get('/rooms/{roomId}', [BookingController::class, 'show'])->name('rooms.show');
Route::get('room_detail/{id}', [RoomDetailController::class, 'show'])->name('room_detail');

Route::middleware(['auth'])->group(function () {
    Route::get('/booking/{roomId}/{book_id}', [BookingController::class, 'show']);
    Route::get('/booking/{roomId}', [BookingController::class, 'show'])->name('booking.show');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
});

Route::get('/user/booking/{booking_id}', [BookingController::class, 'show'])->name('user.booking.show');
Route::post('/booking/store', action: [BookingController::class, 'store'])->name('booking.store');
Route::get('/calendar', [BookingController::class, 'calendar'])->name('calendar');
Route::get('/user/my-bookings', [BookingController::class, 'myBookings'])->middleware('auth')->name('myBookings');
Route::get('/get-reject-reason/{booking_id}', [BookingController::class, 'getRejectReason']);
Route::get('/get-notifications', [BookingController::class, 'getNotifications']);

Route::get('/user/myBooking', [BookingController::class, 'myBookings'])->name('user.myBooking');
Route::get('/user/bookings/{bookId}/reject-reason', [BookingController::class, 'getRejectReason']);

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('room_create', function () {
            return view('admin.room_create');
        })->name('room.create');

        Route::get('room_list', function () {
            return view('admin.room_list');
        })->name('room.list');

        Route::get('room/edit/{id}', function ($id) {
            return view('admin.room_edit', ['roomId' => $id]);
        })->name('room.edit');

        Route::get('room_booking', function () {
            return view('admin.room_booking');
        })->name('room.booking');

        Route::get('bookings', [AdminDashboardController::class, 'bookings'])->name('bookings');
        Route::get('rooms', [AdminDashboardController::class, 'rooms'])->name('rooms');
        Route::get('users', [AdminDashboardController::class, 'users'])->name('users');
        Route::get('settings', [AdminDashboardController::class, 'settings'])->name('settings');

        Route::get('bookings', [AdminBookingController::class, 'index'])->name('booking.index');
        Route::get('bookings/{id}', [AdminBookingController::class, 'show'])->name('booking.show');
        Route::patch('bookings/{id}/status', [AdminBookingController::class, 'updateStatus']);
        Route::get('notifications', [AdminNotificationController::class, 'fetchNotifications']);
        Route::post('notifications/clear', [AdminNotificationController::class, 'clearNotifications'])->name('notifications.clear');
        Route::post('notifications/remove', [AdminNotificationController::class, 'removeNotification'])->name('notifications.remove');
    });
});

Route::post('/notifications/clear', function () {
    Cache::forget('user_notifications_' . auth()->id());
    return response()->json(['message' => 'All notifications cleared']);
})->name('notifications.clear');

Route::post('/notifications/remove', function (Request $request) {
    $userId = auth()->id();
    $index = $request->input('index');
    $notifications = Cache::get("user_notifications_{$userId}", []);

    if (isset($notifications[$index])) {
        unset($notifications[$index]);
        Cache::put("user_notifications_{$userId}", array_values($notifications), now()->addDays(7));
    }

    return response()->json(['message' => 'Notification removed']);
})->name('notifications.remove');

Route::middleware('auth')->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/comments', [CommentController::class, 'store']);
    Route::get('/comments/{bookingId}', [CommentController::class, 'getComments']);
    Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});
