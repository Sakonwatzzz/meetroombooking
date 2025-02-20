<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;

class AdminDashboardController extends Controller
{
    public function adminLogin() {
        return view('admin.dashboard');
    }

    public function index() {
        return view('admin.dashboard'); // ตรวจสอบว่ามีไฟล์ dashboard.blade.php อยู่ใน resources/views/admin/
    }

    //     public function rooms() {
    //     $rooms = Room::all(); // Fetch all rooms from the database
    //     return view('admin.rooms', compact('rooms')); // Ensure rooms.blade.php exists in resources/views/admin/
    // }
}
