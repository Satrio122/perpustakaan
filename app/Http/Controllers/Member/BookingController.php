<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function dataBooking(User $user)
    {
        abort_unless((int) Auth::id() === (int) $user->id, 403);

        $data_booking = Booking::with('booking_detail.buku.kategori')
            ->where('id_user', $user->id)
            ->get();

        if ($data_booking->isEmpty()) {
            return redirect()->route('member.index')->with('info', 'Belum ada booking.');
        }

        return view('member.data_booking', compact('data_booking'));
    }

    public function bookingPdf(User $user)
    {
        abort_unless((int) Auth::id() === (int) $user->id, 403);

        $data_booking = Booking::with('booking_detail.buku.kategori')
            ->where('id_user', $user->id)
            ->get();

        if ($data_booking->isEmpty()) {
            return redirect()->route('member.index')->with('info', 'Belum ada booking untuk dicetak.');
        }

        $pdf = Pdf::loadView('member.booking_pdf', compact('data_booking'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('bukti-booking.pdf');
    }
}
