<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomUserController extends Controller
{

    // ฟังก์ชันสำหรับแสดงข้อมูลใน Blade Template
    public function showRooms()
    {
        $room_data = DB::table('room')->get();
        return view('user.room_list', ['room_data' => $room_data]);
    }

    public function index()
    {
        // ดึงข้อมูลห้องจากฐานข้อมูล
        $room_data = DB::table('room')->get(); // ตรวจสอบให้แน่ใจว่าใช้ชื่อ table ที่ถูกต้อง

        // ส่งข้อมูลในรูปแบบ JSON
        return response()->json($room_data);
    }
    public function __construct()
    {
        $this->middleware('auth');
    }
}
