<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class CommentController extends Controller
{
    // บันทึกคอมเมนต์ใหม่
    public function store(Request $request)
    {
        // ตรวจสอบว่า request มีข้อมูลที่จำเป็นหรือไม่
        $validated = $request->validate([
            'booking_id' => 'required|integer',
            'comment' => 'required|string|max:1000',
        ]);

        // บันทึกข้อมูลคอมเมนต์ (ตัวอย่าง)
        $comment = new Comment();
        $comment->booking_id = $validated['booking_id'];
        $comment->comment = $validated['comment'];
        $comment->user_id = auth()->id();
        $comment->save();

        // ส่งข้อมูล JSON กลับ
        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully!',
            'comment' => $comment
        ], 201); // ส่งสถานะ 201 (Created) ถ้าคอมเมนต์ถูกบันทึก
    }

    // ดึงคอมเมนต์ของการจองห้องนั้นๆ
    public function getComments($bookingId)
    {
        $comments = Comment::with('user', 'replies.user')  // เพิ่มการโหลดข้อมูลผู้ใช้
            ->where('booking_id', $bookingId)
            ->get();

        return response()->json($comments);
    }



    public function index($bookingId)
    {
        $comments = Comment::with(['user'])
            ->where('booking_id', $bookingId)  // ตรวจสอบการจองห้องที่เกี่ยวข้อง
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'user' => [
                    'name' => $comment->user->name,  // ชื่อผู้ใช้
                    'role' => $comment->user->role  // ตรวจสอบ role
                ],
                'parent_id' => $comment->parent_id,  // ตรวจสอบการตอบกลับ
                'replies' => $comment->replies  // คอมเมนต์ตอบกลับ
            ];
        }));
    }

    public function apiBookingDetails($bookingId)
    {
        // ค้นหาการจองห้องที่ต้องการ
        $booking = Booking::findOrFail($bookingId);

        // ส่งตัวแปรไปยัง Blade view
        return response()->json(['booking' => $booking]);
    }
    // Add comment reply
    public function reply(Request $request, $commentId)
    {
        try {
            // ตรวจสอบข้อมูลที่ส่งเข้ามา
            $validated = $request->validate([
                'reply' => 'required|string',
            ]);

            // ตรวจสอบว่าคอมเมนต์ต้นทางมีอยู่จริง
            $comment = Comment::find($commentId);
            if (!$comment) {
                return response()->json(['success' => false, 'message' => 'Comment not found'], 404);
            }

            // ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือยัง
            if (!auth()->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // สร้างคอมเมนต์ใหม่เป็นการตอบกลับ
            $reply = new Comment();
            $reply->comment = $validated['reply'];
            $reply->user_id = auth()->id();
            $reply->parent_id = $commentId;
            $reply->booking_id = null; // หรือ
            $reply->parent_id = $commentId;
            $reply->save();

            return response()->json([
                'success' => true,
                'message' => 'Reply added successfully!',
                'reply' => $reply
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // Delete comment
    // รับ parameter เป็น comment
    public function destroy(Comment $comment)
    {
        // ตรวจสอบว่า comment เป็นของผู้ใช้ที่ล็อกอินอยู่หรือไม่
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You can only delete your own comment.'], 403);
        }

        // หากเป็นคอมเมนต์ของผู้ใช้ที่ล็อกอินอยู่ ให้ทำการลบ
        $comment->delete();
        return response()->json(['success' => true, 'message' => 'Comment deleted successfully']);
    }

    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You can only edit your own comment.'], 403);
        }

        // หากเป็นคอมเมนต์ของผู้ใช้ที่ล็อกอินอยู่ ให้ทำการแก้ไข
        $comment->comment = $validated['comment'];
        $comment->save();

        return response()->json(['success' => true, 'message' => 'Comment updated successfully', 'comment' => $comment]);
    }
}
