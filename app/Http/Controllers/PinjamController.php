<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Buku;
use App\Models\Pinjam;
use App\Models\PinjamDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\PinjamExport;

class PinjamController extends Controller
{
    public function index()
    {
        return view('admin.pinjam.index');
    }

    public function getData(Request $request)
    {
        abort_unless($request->ajax(), 404);

        $records = $this->activeLoanRecords($request);
        $flattenedData = $records->flatMap(function ($pinjam) {
            return $pinjam->pinjam_detail->map(function ($detail) use ($pinjam) {
                return [
                    'no_pinjam' => $pinjam->no_pinjam,
                    'tgl_pinjam' => $pinjam->tgl_pinjam,
                    'tgl_kembali' => $detail->tgl_kembali,
                    'judul_buku' => $detail->buku->judul_buku ?? '',
                    'status' => $detail->status ?? '',
                    'gambar' => isset($detail->buku->image)
                        ? asset('storage/' . $detail->buku->image)
                        : '',
                    'anggota' => $pinjam->anggota->nama ?? 'Tidak Ada Anggota',
                    'petugas' => $pinjam->petugas_pinjam->nama ?? 'Tidak Ada Petugas',
                    'id_buku' => $detail->buku->id ?? '',
                ];
            });
        });

        return DataTables::of($flattenedData)
            ->addIndexColumn()
            ->addColumn('tgl_pinjam', fn($row) => Carbon::parse($row['tgl_pinjam'])->format('d-m-Y'))
            ->addColumn('tgl_kembali', fn($row) => $row['tgl_kembali']
                ? Carbon::parse($row['tgl_kembali'])->format('d-m-Y')
                : '')
            ->addColumn('judul_buku', fn($row) => $row['judul_buku'])
            ->addColumn('status', fn($row) => $row['status'])
            ->addColumn('gambar', function ($row) {
                return $row['gambar']
                    ? '<img src="' . e($row['gambar']) . '" alt="Cover buku" style="max-width: 60px; max-height: 80px">'
                    : '';
            })
            ->addColumn('anggota', fn($row) => $row['anggota'])
            ->addColumn('petugas', fn($row) => $row['petugas'])
            ->addColumn('aksi', function ($row) {
                $url = route('admin.transaksi.pinjam.kembalikanBuku', [
                    'no_pinjam' => $row['no_pinjam'],
                    'id_buku' => $row['id_buku'],
                ]);

                return '<form action="' . e($url) . '" method="POST" class="d-inline">'
                    . csrf_field() . method_field('PUT')
                    . '<button type="submit" class="btn btn-success btn-sm kembalikan-buku"'
                    . ' data-toggle="tooltip" title="Kembalikan buku">'
                    . '<i class="fas fa-undo"></i></button></form>';
            })
            ->rawColumns(['gambar', 'aksi'])
            ->make(true);
    }

    public function exportPdfPinjam(Request $request)
    {
        $data_pinjam = $this->activeLoanRecords($request, true);

        $pdf = Pdf::loadView('admin.pinjam.pinjam_pdf', compact('data_pinjam'));

        return $pdf->download('transaksi_pinjam.pdf');
    }

    public function exportExcelPinjam(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        return Excel::download(
            new PinjamExport($validated['start_date'], $validated['end_date']),
            'datapinjam.xlsx'
        );
    }

    public function kembalikanBuku(string $no_pinjam, int $id_buku)
    {
        $returned = DB::transaction(function () use ($no_pinjam, $id_buku) {
            $detail = PinjamDetail::where('no_pinjam', $no_pinjam)
                ->where('id_buku', $id_buku)
                ->where('status', 'Pinjam')
                ->lockForUpdate()
                ->first();

            if (!$detail) {
                return false;
            }

            $book = Buku::whereKey($id_buku)->lockForUpdate()->firstOrFail();

            $detail->status = 'Kembali';
            $detail->tgl_pengembalian = Carbon::now();
            $detail->id_petugas_kembali = Auth::id();
            $detail->save();

            $book->stok++;
            $book->dipinjam = max(0, $book->dipinjam - 1);
            $book->save();

            return true;
        }, 3);

        if (!$returned) {
            return response()->json(['error' => 'Detail pinjaman tidak ditemukan.'], 404);
        }

        return response()->json(['success' => 'Buku berhasil dikembalikan.']);
    }

    public function pengembalian_index()
    {
        $data_pinjam = Pinjam::with([
            'pinjam_detail' => fn($query) => $query->where('status', 'Kembali'),
            'pinjam_detail.buku',
            'pinjam_detail.petugas_kembali',
            'petugas_pinjam',
        ])
            ->whereHas('pinjam_detail', fn($query) => $query->where('status', 'Kembali'))
            ->orderByDesc('no_pinjam')
            ->get();

        return view('admin.pinjam.pengembalian_index', compact('data_pinjam'));
    }

    private function activeLoanRecords(Request $request, bool $requireDates = false)
    {
        $dateRules = $requireDates ? ['required', 'date'] : ['nullable', 'date'];
        $dates = $request->validate([
            'start_date' => $dateRules,
            'end_date' => $dateRules,
        ]);

        $query = Pinjam::with([
            'pinjam_detail' => fn($query) => $query->where('status', 'Pinjam'),
            'pinjam_detail.buku',
            'pinjam_detail.petugas_kembali',
            'petugas_pinjam',
            'anggota',
        ])->whereHas('pinjam_detail', fn($query) => $query->where('status', 'Pinjam'));

        if (!empty($dates['start_date']) && !empty($dates['end_date'])) {
            $startDate = Carbon::parse($dates['start_date'])->startOfDay();
            $endDate = Carbon::parse($dates['end_date'])->endOfDay();

            if ($endDate->lt($startDate)) {
                throw ValidationException::withMessages([
                    'end_date' => 'Tanggal akhir harus sama atau setelah tanggal awal.',
                ]);
            }

            $query->whereBetween('tgl_pinjam', [$startDate, $endDate]);
        }

        return $query->orderByDesc('no_pinjam')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_booking' => ['required', 'string', 'exists:booking,id_booking'],
            'denda' => ['required', 'array', 'min:1'],
            'denda.*' => ['required', 'numeric', 'min:0'],
            'lama' => ['required', 'array', 'min:1'],
            'lama.*' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated) {
            $booking = Booking::where('id_booking', $validated['id_booking'])
                ->lockForUpdate()
                ->firstOrFail();
            $bookingDetails = BookingDetail::where('id_booking', $booking->id_booking)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $dendaValues = array_values($validated['denda']);
            $lamaValues = array_values($validated['lama']);

            if ($bookingDetails->isEmpty() || count($dendaValues) !== $bookingDetails->count() || count($lamaValues) !== $bookingDetails->count()) {
                throw ValidationException::withMessages([
                    'id_booking' => 'Data tarif dan lama pinjam harus tersedia untuk setiap buku booking.',
                ]);
            }

            $books = Buku::whereIn('id', $bookingDetails->pluck('id_buku'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($bookingDetails as $detail) {
                $book = $books->get($detail->id_buku);

                if (!$book || $book->dibooking < 1) {
                    throw ValidationException::withMessages([
                        'id_booking' => 'Stok booking untuk salah satu buku tidak valid.',
                    ]);
                }
            }

            $now = Carbon::now();
            $sequence = Pinjam::whereDate('created_at', $now->toDateString())->count() + 1;
            do {
                $noPinjam = 'P' . $now->format('ymd') . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
                $sequence++;
            } while (Pinjam::where('no_pinjam', $noPinjam)->exists());

            Pinjam::create([
                'no_pinjam' => $noPinjam,
                'tgl_pinjam' => $now,
                'id_booking' => $booking->id_booking,
                'id_user' => $booking->id_user,
                'id_petugas_pinjam' => Auth::id(),
            ]);

            foreach ($bookingDetails as $index => $detail) {
                $lamaPinjam = (int) $lamaValues[$index];

                PinjamDetail::create([
                    'no_pinjam' => $noPinjam,
                    'id_buku' => $detail->id_buku,
                    'tgl_kembali' => $now->copy()->addDays($lamaPinjam),
                    'tgl_pengembalian' => null,
                    'denda' => $dendaValues[$index],
                    'lama_pinjam' => $lamaPinjam,
                    'status' => 'Pinjam',
                ]);

                $book = $books->get($detail->id_buku);
                $book->dibooking--;
                $book->dipinjam++;
                $book->save();
            }

            $booking->booking_detail()->delete();
            $booking->delete();
        }, 3);

        return redirect()->route('admin.transaksi.booking.index')
            ->with('success', 'Data pinjaman berhasil disimpan dan booking telah dihapus.');
    }

    public function sedangPinjam(User $user)
    {
        abort_unless((int) Auth::id() === (int) $user->id, 403);

        $sedang_pinjam = Pinjam::with([
            'pinjam_detail' => fn($query) => $query->where('status', 'Pinjam'),
            'pinjam_detail.buku.kategori',
            'pinjam_detail.petugas_kembali',
            'petugas_pinjam',
        ])
            ->where('id_user', $user->id)
            ->whereHas('pinjam_detail', fn($query) => $query->where('status', 'Pinjam'))
            ->orderBy('no_pinjam')
            ->get();

        return view('member.sedang_pinjam', compact('sedang_pinjam'));
    }

    public function riwayatPinjam(User $user)
    {
        abort_unless((int) Auth::id() === (int) $user->id, 403);

        $riwayat_pinjam = Pinjam::with([
            'pinjam_detail.buku.kategori',
            'pinjam_detail.petugas_kembali',
            'petugas_pinjam',
        ])
            ->where('id_user', $user->id)
            ->whereHas('pinjam_detail')
            ->orderBy('no_pinjam')
            ->get();

        return view('member.riwayat_pinjam', compact('riwayat_pinjam'));
    }
}
