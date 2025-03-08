<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;

class RoomDetailController extends Controller
{
    public function show($room_id)
    {
        $room = Room::where('id', $room_id)->firstOrFail();
        $booking = Booking::where('room_id', $room_id)->first(); // ใช้ $room_id แทน $id

        return view('user.room_detail', compact('room','booking'));
    }
}
