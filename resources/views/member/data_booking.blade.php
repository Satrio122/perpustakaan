@extends('member.layout.main')

@section('title', 'Data Booking')

@section('content')
<main class="container pt-4">
    <style>
        @media print {

            .navbar,
            .footer,
            .print-hidden {
                display: none !important;
            }
        }
    </style>

    <div class="alert alert-info">
        Waktu pengambilan buku maksimal 1 x 24 jam setelah booking. Jika tidak diambil, booking dapat dibatalkan oleh sistem.
    </div>
    <div class="mb-3 print-hidden">
        <a href="{{ route('member.bookingPdf', $data_booking->first()->id_user) }}" class="btn btn-primary">
            <i class="fas fa-print" aria-hidden="true"></i> Cetak Bukti
        </a>
    </div>

    @foreach ($data_booking as $booking)
    <section class="mb-4">
        <h5>Booking {{ $booking->id_booking }}</h5>
        <p class="mb-2">Tanggal booking: {{ $booking->tgl_booking }} | Batas ambil: {{ $booking->batas_ambil }}</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped bg-white">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul Buku</th>
                        <th>Kategori</th>
                        <th>Pengarang</th>
                        <th>Penerbit</th>
                        <th>Tahun Terbit</th>
                        <th>Gambar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($booking->booking_detail as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->buku->judul_buku ?? 'Buku tidak tersedia' }}</td>
                        <td>{{ $detail->buku->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $detail->buku->pengarang ?? '-' }}</td>
                        <td>{{ $detail->buku->penerbit ?? '-' }}</td>
                        <td>{{ $detail->buku->tahun_terbit ?? '-' }}</td>
                        <td>
                            @if ($detail->buku)
                            <img src="{{ asset('storage/' . $detail->buku->image) }}" alt="Cover {{ $detail->buku->judul_buku }}" style="height: 90px; width: 64px; object-fit: cover;">
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endforeach
</main>
@endsection