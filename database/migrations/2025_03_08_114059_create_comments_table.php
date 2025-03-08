<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id(); // id ของคอมเมนต์
            $table->unsignedBigInteger('booking_id')->nullable(); // ลบ `.change()` ออกไป
            $table->unsignedBigInteger('user_id'); // คอลัมน์สำหรับเชื่อมโยงกับตาราง users
            $table->text('comment'); // เนื้อหาคอมเมนต์
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade'); // ✅ ใช้อันนี้เท่านั้น
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('booking_id')->references('book_id')->on('booking')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
