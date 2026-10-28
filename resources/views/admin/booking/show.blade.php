@extends('admin.layout.main')

@section('title', 'Data Booking')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Booking</h3>
        </div>

        <div class="card-body">
            <div class="alert alert-warning" role="alert">
                Waktu pengambilan buku 1x24 jam dari booking. Jika tidak diambil setelah batas waktu,
                booking akan dibatalkan otomatis oleh sistem.
            </div>

            @php($booking = $data_booking->first())

            <div class="row mb-4">
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">ID Booking</dt>
                        <dd class="col-sm-8">{{ $booking->id_booking }}</dd>
                        <dt class="col-sm-4">Tanggal Booking</dt>
                        <dd class="col-sm-8">{{ $booking->tgl_booking }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Batas Ambil</dt>
                        <dd class="col-sm-8">{{ $booking->batas_ambil }}</dd>
                        <dt class="col-sm-4">Anggota</dt>
                        <dd class="col-sm-8">{{ $booking->anggota->nama }}</dd>
                    </dl>
                </div>
            </div>

            <form action="{{ route('admin.transaksi.peminjaman.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_booking" value="{{ $booking->id_booking }}">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul Buku</th>
                                <th>Kategori</th>
                                <th>Pengarang</th>
                                <th>Penerbit</th>
                                <th>Tahun Terbit</th>
                                <th>Denda/Buku/Hari</th>
                                <th>Lama Pinjam</th>
                                <th>Gambar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data_booking as $booking)
                            @foreach ($booking->booking_detail as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $detail->buku->judul_buku }}</td>
                                <td>{{ $detail->buku->kategori->nama_kategori }}</td>
                                <td>{{ $detail->buku->pengarang }}</td>
                                <td>{{ $detail->buku->penerbit }}</td>
                                <td>{{ $detail->buku->tahun_terbit }}</td>
                                <td>
                                    <input type="number" name="denda[]" class="form-control"
                                        min="0" step="0.01" required aria-label="Denda per buku per hari">
                                </td>
                                <td>
                                    <input type="number" name="lama[]" class="form-control"
                                        min="1" step="1" required aria-label="Lama pinjam dalam hari">
                                </td>
                                <td>
                                    <img src="{{ asset('storage/' . $detail->buku->image) }}"
                                        alt="Cover {{ $detail->buku->judul_buku }}"
                                        class="img-thumbnail" style="max-width: 90px; max-height: 120px;"
                                        loading="lazy">
                                </td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-handshake"></i> Proses Pinjam
                </button>
            </form>

            <a href="{{ url()->previous() }}" class="btn btn-secondary mt-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection