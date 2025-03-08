<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class AdminDashboardController extends Controller
{
    public function adminLogin()
    {
        return view('admin.dashboard');
    }

    public function index()
    {
        $pendingCount = Booking::where('bookstatus', 'pending')->count();
        $approvedCount = Booking::where('bookstatus', 'approved')->count();
        $rejectedCount = Booking::where('bookstatus', 'rejected')->count();

        dd($pendingCount, $approvedCount, $rejectedCount); // ตรวจสอบผลลัพธ์

        return view('admin.dashboard', [
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
        ]);
    }
}
