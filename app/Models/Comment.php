<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'user_id', 'comment', 'parent_id'];

    // ความสัมพันธ์กับการจอง
    public function booking() {
        return $this->belongsTo(Booking::class);
    }

    // ความสัมพันธ์กับผู้ใช้
    public function user() {
        return $this->belongsTo(User::class);
    }

    // ความสัมพันธ์กับคอมเมนต์หลัก
    public function parent() {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // ดึงคอมเมนต์ที่เป็นการตอบกลับ
    public function replies() {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
