<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminRoomController;
use App\Http\Controllers\admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\user\BookingController;
use App\Http\Controllers\admin\AdminBookingController;
use App\Http\Controllers\user\UserNotificationController;
use App\Http\Controllers\admin\AdminNotificationController;
use App\Http\Controllers\user\UserDashboardController;
use App\Http\Controllers\user\RoomUserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReplyController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * 🔹 Admin Authentication Routes
 */
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');
});

/**
 * 🔹 Admin Protected Routes (Require Authentication)
 */
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/room-usage', [AdminDashboardController::class, 'getRoomUsage']);

    // Rooms Management - explicitly define the routes
    Route::get('/rooms', [AdminRoomController::class, 'index']);
    Route::post('/rooms', [AdminRoomController::class, 'store']);
    Route::get('/rooms/{id}', [AdminRoomController::class, 'show']);
    Route::put('/rooms/{id}', [AdminRoomController::class, 'update']);
    Route::delete('/rooms/{id}', [AdminRoomController::class, 'destroy']);
    Route::post('/rooms/{id}', [AdminRoomController::class, 'update']);

    // การจัดการการจอง
    Route::get('/bookings', [AdminBookingController::class, 'getBookings']);
    Route::get('/bookings/{id}', [AdminBookingController::class, 'show']);
    Route::patch('/bookings/{id}/status', [AdminBookingController::class, 'updateStatus']);
    Route::get('/bookings/{id}/reject-reason', [AdminBookingController::class, 'getRejectReason']);

    Route::get('/notifications', [AdminNotificationController::class, 'fetchNotifications']);
    Route::delete('/notifications/{index}', [AdminNotificationController::class, 'removeNotification']);
    Route::delete('/notifications', [AdminNotificationController::class, 'clearNotifications']);

    // แสดงปฏิทิน
    Route::get('/events', [AdminBookingController::class, 'getEvents']);
});

Route::post('/register', [RegisteredUserController::class, 'register']);
Route::post('/login', [RegisteredUserController::class, 'login']);
Route::post('/logout', [RegisteredUserController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/user/{user_id}/dashboard', [UserDashboardController::class, 'getUserDashboardById']);
    Route::get('/user/bookings', [UserDashboardController::class, 'getUserBookings']);
    Route::get('/user/notifications', [UserDashboardController::class, 'getNotifications']);
    Route::get('/user/booking/reject-reason/{booking_id}', [UserDashboardController::class, 'getRejectReason']);

    Route::get('/user/myBookings/data', [BookingController::class, 'getUserBookings'])->name('user.bookings.data');
});

Route::get('/calendar', [BookingController::class, 'calendar'])->name('calendar');
Route::get('/get-events', [BookingController::class, 'getEvents'])->name('get-events');
Route::get('/booking/events', [BookingController::class, 'getEvents'])->name('booking.events');
Route::get('/book_detail', [BookingController::class, 'detail'])->name('booking.detail');
Route::get('/booking/{booking_id}', [BookingController::class, 'show'])->name('booking.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/booking/store', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::get('/get-reject-reason/{booking_id}', [BookingController::class, 'getRejectReason']);
    Route::get('/get-notifications', [BookingController::class, 'getNotifications']);
});

// ตั้งค่า route สำหรับการดึงข้อมูลห้องประชุมที่พร้อมจอง
Route::get('/rooms/available', [BookingController::class, 'showAvailableRooms']);
Route::get('/api/rooms', [RoomUserController::class, 'getRooms']);

Route::middleware('auth:sanctum')->post('/room/{bookingId}/comment', [CommentController::class, 'storeComment']);
Route::get('/room/{roomId}/comments', [CommentController::class, 'getComments']);
Route::post('/comment/{commentId}/reply', [CommentController::class, 'storeReply']);
Route::put('/comment/{commentId}/update', [CommentController::class, 'updateComment']);
Route::delete('/comment/{commentId}/delete', [CommentController::class, 'deleteComment']);
Route::get('/comments/{bookingId}/replies', [CommentController::class, 'getReplies']);
Route::get('/comments/{bookingId}/replies', [CommentController::class, 'getCommentsWithReplies']);
Route::get('/search-rooms', [RoomUserController::class, 'searchRooms']);

// API สำหรับดึงเหตุผลการปฏิเสธ
Route::get('/user/bookings/{booking_id}/reject-reason', [BookingController::class, 'getRejectReason'])->name('user.bookings.reject-reason');
