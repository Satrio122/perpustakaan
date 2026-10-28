@extends('admin.layout.main')

@section('title', 'Transaksi Pengembalian')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Pengembalian Buku</h3>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
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
                        @foreach ($data_pinjam as $pinjam)
                        @foreach ($pinjam->pinjam_detail as $detail)
                        @php
                        $tglKembali = \Carbon\Carbon::parse($detail->tgl_kembali);
                        $tglPengembalian = $detail->tgl_pengembalian
                        ? \Carbon\Carbon::parse($detail->tgl_pengembalian)
                        : null;
                        $terlambat = $tglPengembalian && $tglPengembalian->gt($tglKembali)
                        ? (int) round($tglKembali->diffInDays($tglPengembalian))
                        : 0;
                        $totalDenda = $detail->denda * $terlambat;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $pinjam->no_pinjam }}</td>
                            <td>{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d-m-Y') }}</td>
                            <td>{{ $detail->tgl_kembali ? \Carbon\Carbon::parse($detail->tgl_kembali)->format('d-m-Y') : '-' }}</td>
                            <td>{{ $detail->lama_pinjam }} hari</td>
                            <td>{{ $tglPengembalian?->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $detail->buku->judul_buku ?? '-' }}</td>
                            <td><span class="badge badge-success">{{ $detail->status }}</span></td>
                            <td>
                                <strong>Denda:</strong> Rp {{ number_format($detail->denda, 0, ',', '.') }} / hari<br>
                                <strong>Terlambat:</strong> {{ $terlambat }} hari<br>
                                <strong>Total denda:</strong> Rp {{ number_format($totalDenda, 0, ',', '.') }}
                            </td>
                            <td>
                                @if ($detail->buku?->image)
                                <img src="{{ asset('storage/' . $detail->buku->image) }}"
                                    alt="Cover {{ $detail->buku->judul_buku }}"
                                    class="img-thumbnail" style="max-width: 70px; max-height: 95px;"
                                    loading="lazy">
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                <strong>Pinjam:</strong> {{ $pinjam->petugas_pinjam->nama ?? '-' }}<br>
                                <strong>Kembali:</strong> {{ $detail->petugas_kembali->nama ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#example1').DataTable({
            responsive: true,
            autoWidth: false,
        });

        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush