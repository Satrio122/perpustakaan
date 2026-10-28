<?php

namespace App\Http\Controllers\Member;

use App\Models\Temp;
use App\Models\Booking;
use App\Models\Buku;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class TempController extends Controller
{
    public function index()
    {
        $keranjang = Temp::with('buku.kategori')
            ->where('id_user', Auth::id())
            ->get();

        return view('member.keranjang', compact('keranjang'));
    }

    public function dataKeranjang(User $user)
    {
        abort_unless((int) Auth::id() === (int) $user->id, 403);

        $data_keranjang = Temp::with('buku.kategori')
            ->where('id_user', $user->id)
            ->get();

        if ($data_keranjang->isEmpty()) {
            return redirect()->route('member.index')->with('info', 'Tidak ada buku di keranjang.');
        }

        return view('member.data_keranjang', compact('data_keranjang'));
    }

    public function tambahKeranjang(Request $request)
    {
        $userId = Auth::id();
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:buku,id'],
        ]);
        $buku = Buku::findOrFail($validated['id']);

        if ($buku->stok < 1) {
            return redirect()->route('member.index')->with('error', 'Buku sedang tidak tersedia.');
        }

        // Cek apakah buku yang diklik booking dengan user yang sedang login sudah ada di tabel temp atau belum         
        $cek_keranjang = Temp::where(['id_buku' => $buku->id, 'id_user' => $userId])->count();

        $cek_pinjam = DB::table('pinjam as a')->join('pinjam_detail as b', 'a.no_pinjam', '=', 'b.no_pinjam')->where('a.id_user', $userId)->where('b.id_buku', $buku->id)->where('b.status', 'Pinjam')->count();

        // Jika buku yang diklik booking sudah ada di temp         
        if ($cek_keranjang > 0 || $cek_pinjam > 0) {
            return redirect()->route('member.index')->with('error', 'Buku sudah ada di keranjang atau sedanag dipinjam');
        }

        // Cek apakah ada data buku yang belum diambil (masih booking)         
        $cek_booking = Booking::where('id_user', $userId)->count();
        if ($cek_booking > 0) {
            return redirect()->route('member.index')->with('error', 'Masih ada buku yang belum diambil, silakan ambil buku atau menunggu pembatalan otomatis');
        }

        // Cek jumlah buku dalam keranjang         
        $cek_limit = Temp::where(['id_user' => $userId])->count();

        // Cek jumlah buku yang sedang dipinjam         
        $total_pinjam = DB::table('pinjam as a')->join('pinjam_detail as b', 'a.no_pinjam', '=', 'b.no_pinjam')->where('a.id_user', $userId)->where('b.status', 'Pinjam')->count();

        // Jika jumlah keranjang + booking + pinjam sama dengan/lebih dari 3         
        if (($total_pinjam + $cek_booking + $cek_limit) >= 3) {
            return redirect()->route('member.index')->with('error', 'Total buku di keranjang, booking dan sedang pinjam tidak boleh lebih dari 3');
        }
        $data['id_buku'] = $buku->id;
        $data['id_user'] = $userId;
        Temp::create($data);
        return redirect()->route('member.index')->with('success', 'Buku berhasil ditambahkan ke keranjang!');
    }

    public function hapusKeranjangById(int $id)
    {
        $item = Temp::where('id', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $item->delete();

        return redirect()->route('member.keranjang')->with('success', 'Buku dihapus dari keranjang.');
    }

    public function hapusKeranjang(Buku $buku, User $user)
    {
        abort_unless((int) Auth::id() === (int) $user->id, 403);

        $deleted = Temp::where('id_buku', $buku->id)
            ->where('id_user', $user->id)
            ->delete();

        if (!$deleted) {
            return back()->with('error', 'Buku tidak ditemukan di keranjang.');
        }

        return back()->with('success', 'Buku berhasil dihapus dari keranjang!');
    }

    public function simpanBooking(Request $request)
    {
        $userId = (int) Auth::id();

        try {
            $newIdBooking = DB::transaction(function () use ($userId) {
                $items = Temp::where('id_user', $userId)
                    ->lockForUpdate()
                    ->get();

                if ($items->isEmpty()) {
                    throw new \DomainException('Keranjang masih kosong.');
                }

                $books = [];

                foreach ($items as $item) {
                    $buku = Buku::whereKey($item->id_buku)->lockForUpdate()->first();

                    if (!$buku || $buku->stok < 1) {
                        throw new \DomainException('Ada buku yang stoknya kosong, silakan hapus terlebih dahulu.');
                    }

                    $books[] = [$item, $buku];
                }

                $today = Carbon::today();
                $latestBooking = DB::table('booking')
                    ->whereDate('tgl_booking', $today)
                    ->orderByDesc('id')
                    ->first();
                $latestPinjam = DB::table('pinjam')
                    ->whereDate('tgl_pinjam', $today)
                    ->orderByDesc('id')
                    ->first();

                $latestBookingId = $latestBooking
                    ? (int) substr($latestBooking->id_booking, -3)
                    : 0;
                $latestPinjamId = $latestPinjam
                    ? (int) substr($latestPinjam->id_booking, -3)
                    : 0;
                $sequence = max($latestBookingId, $latestPinjamId) + 1;
                $newIdBooking = 'B' . $today->format('ymd') . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
                $now = Carbon::now();

                DB::table('booking')->insert([
                    'id_booking' => $newIdBooking,
                    'tgl_booking' => $now,
                    'batas_ambil' => $now->copy()->addDay(),
                    'id_user' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($books as [$item, $buku]) {
                    DB::table('booking_detail')->insert([
                        'id_booking' => $newIdBooking,
                        'id_buku' => $buku->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('buku')->where('id', $buku->id)->update([
                        'stok' => DB::raw('stok - 1'),
                        'dibooking' => DB::raw('dibooking + 1'),
                        'updated_at' => $now,
                    ]);
                }

                Temp::where('id_user', $userId)->delete();

                return $newIdBooking;
            });
        } catch (\DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Terjadi kesalahan saat menyimpan booking.');
        }

        return redirect()
            ->route('member.dataBooking', $userId)
            ->with('success', 'Booking ' . $newIdBooking . ' berhasil disimpan!');
    }
}
