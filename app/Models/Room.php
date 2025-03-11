<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Booking; // นำเข้า Booking Model

class Room extends Model
{
    use HasFactory;

    protected $table = 'room'; // กำหนดชื่อตาราง
    protected $fillable = [
        'room_name',      // ชื่อห้องประชุม
        'room_detail',    // ความจุของห้อง
        'room_status',    // สถานะของห้อง (available, booked, under maintenance)
        'room_pic'
    ];

    public function images()
    {
        return $this->hasMany(RoomImage::class, 'room_id');
    }

    // ความสัมพันธ์กับ Booking (ห้องหนึ่งห้องสามารถมีการจองหลายครั้ง)
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
