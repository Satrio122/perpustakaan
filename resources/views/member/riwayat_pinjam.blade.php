@extends('member.layout.main')

@section('title', 'Riwayat Peminjaman')

@section('content')
<main class="container pt-4">
    @if ($riwayat_pinjam->isEmpty())
    <div class="alert alert-info">Riwayat peminjaman masih kosong. Booking belum tercatat sebagai peminjaman.</div>
    @else
    <div class="table-responsive">
        <table id="riwayat-pinjam" class="table table-bordered table-striped bg-white">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Pinjam</th>
                    <th>Tgl. Pinjam</th>
                    <th>Tgl. Kembali</th>
                    <th>Lama Pinjam</th>
                    <th>Tgl. Pengembalian</th>
                    <th>Judul Buku</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Gambar</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($riwayat_pinjam as $pinjam)
                @foreach ($pinjam->pinjam_detail as $detail)
                @php
                $tglKembali = $detail->tgl_kembali ? \Carbon\Carbon::parse($detail->tgl_kembali)->startOfDay() : null;
                $tglPerhitungan = $detail->tgl_pengembalian ? \Carbon\Carbon::parse($detail->tgl_pengembalian)->startOfDay() : now()->startOfDay();
                $terlambat = $tglKembali && $tglPerhitungan->gt($tglKembali) ? $tglKembali->diffInDays($tglPerhitungan) : 0;
                $totalDenda = $terlambat * (float) $detail->denda;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pinjam->no_pinjam }}</td>
                    <td>{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d-m-Y') }}</td>
                    <td>{{ $tglKembali?->format('d-m-Y') ?? '-' }}</td>
                    <td>{{ $detail->lama_pinjam }} hari</td>
                    <td>{{ $detail->tgl_pengembalian ? \Carbon\Carbon::parse($detail->tgl_pengembalian)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $detail->buku->judul_buku ?? 'Buku tidak tersedia' }}</td>
                    <td><span class="badge {{ $detail->status === 'Kembali' ? 'badge-success' : 'badge-warning' }}">{{ $detail->status }}</span></td>
                    <td>
                        Denda/hari: Rp{{ number_format((float) $detail->denda, 0, ',', '.') }}<br>
                        Terlambat: {{ $terlambat }} hari<br>
                        Total: Rp{{ number_format($totalDenda, 0, ',', '.') }}
                    </td>
                    <td>
                        @if ($detail->buku)
                        <img src="{{ asset('storage/' . $detail->buku->image) }}" alt="Cover {{ $detail->buku->judul_buku }}" style="height: 80px; width: 56px; object-fit: cover;">
                        @endif
                    </td>
                    <td>
                        Pinjam: {{ $pinjam->petugas_pinjam->nama ?? '-' }}<br>
                        Kembali: {{ $detail->petugas_kembali->nama ?? '-' }}
                    </td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</main>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#riwayat-pinjam').DataTable({
            responsive: true,
            autoWidth: false
        });
    });
</script>
@endpush