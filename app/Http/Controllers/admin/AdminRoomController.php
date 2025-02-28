<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Admin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        // ตรวจสอบข้อมูลที่ส่งมา
        $request->validate([
            'room_name' => 'required|string|max:255',
            'room_detail' => 'required|string',
            'room_status' => 'required|string|in:available,booked,under maintenance',
            'room_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Handle image upload
        if ($request->hasFile('room_pic')) {
            $path = $request->file('room_pic')->store('room_pics', 'public');
        } else {
            $path = null;
        }

        // Create Room
        $room = Room::create([
            'room_name' => $request->room_name,
            'room_detail' => $request->room_detail,
            'room_status' => $request->room_status,
            'room_pic' => $path,
        ]);

        return response()->json([
            'message' => 'Room created successfully',
            'room' => $room
        ], 201);
    }

    public function show(string $id)
    {
        $room = Room::findOrFail($id);
        return response()->json($room);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        // Log ค่าปัจจุบันก่อนอัปเดต
        Log::info("🔍 Current Room Data:", $room->toArray());

        // ตรวจสอบค่าที่ส่งเข้ามา
        Log::info("📥 Data Received for Update:", $request->all());

        $validatedData = $request->validate([
            'room_name' => 'sometimes|string|max:255',
            'room_detail' => 'sometimes|string',
            'room_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $dataToUpdate = [];

        if ($request->has('room_name') && $request->room_name !== $room->room_name) {
            $dataToUpdate['room_name'] = $request->room_name;
        }

        if ($request->has('room_detail') && $request->room_detail !== $room->room_detail) {
            $dataToUpdate['room_detail'] = $request->room_detail;
        }

        if ($request->hasFile('room_pic')) {
            if ($room->room_pic) {
                Storage::disk('public')->delete($room->room_pic);
            }
            $dataToUpdate['room_pic'] = $request->file('room_pic')->store('room_pics', 'public');
        }

        // Log ค่าที่จะอัปเดต
        Log::info("🛠 Data to be Updated:", $dataToUpdate);

        if (!empty($dataToUpdate)) {
            $room->update($dataToUpdate);
            Log::info("✅ Room Updated Successfully:", $room->fresh()->toArray());
        } else {
            Log::warning("⚠️ No Data Changed for Room ID: {$id}");
        }

        return response()->json([
            'message' => 'Room updated successfully',
            'room' => $room
        ], 200);
    }

    public function destroy($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        if ($room->room_pic) {
            Storage::disk('public')->delete($room->room_pic);
        }

        $room->delete();

        return response()->json(['message' => 'Room deleted successfully'], 200);
    }
}
