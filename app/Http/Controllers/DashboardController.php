<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index()
    {
        // Booking yang sudah berakhir (kalau mau yang aktif, ganti kondisinya)
        $endBooking = Booking::whereDate('end_date', '<', Carbon::today())->get();

        // Ambil storage available sebagai Collection (bukan array of stdClass)
        $storageAvailable = DB::table('tb_storage_management')
            ->where('status', 'available') // pastikan kolomnya ada
            ->select('id', 'storage_id', 'status', 'last_clean', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        return view('dashboard.index', compact('endBooking', 'storageAvailable'));
    }

    public function admin(){
        return $this->index();
    }
}
