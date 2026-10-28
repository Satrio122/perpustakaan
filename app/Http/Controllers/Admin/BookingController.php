<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $booking = Booking::with('anggota')->get();

        return view('admin.booking.index', compact('booking'));
    }

    public function show(string $id)
    {
        $data_booking = Booking::with([
            'booking_detail' => fn($query) => $query->orderBy('id'),
            'booking_detail.buku.kategori',
            'anggota',
        ])
            ->where('id', $id)
            ->get();

        abort_if($data_booking->isEmpty(), 404);

        return view('admin.booking.show', compact('data_booking'));
    }

    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);

        DB::transaction(function () use ($booking) {
            $booking->booking_detail()->delete();
            $booking->delete();
        });

        return redirect()->route('admin.transaksi.booking.index')
            ->with('success', 'Booking berhasil dihapus.');
    }
}
