@extends('admin.layout.main')

@section('title', 'Transaksi Peminjaman')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Peminjaman Aktif</h3>
        </div>

        <div class="card-body">
            <div class="form-row align-items-end mb-3">
                <div class="form-group col-md-3">
                    <label for="start_date">Tanggal Awal</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label for="end_date">Tanggal Akhir</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <button type="button" id="filter" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button type="button" id="export-pdf" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" id="export-excel" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="transaksi-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Pinjam</th>
                            <th>Tgl. Pinjam</th>
                            <th>Tgl. Kembali</th>
                            <th>Judul Buku</th>
                            <th>Status</th>
                            <th>Gambar</th>
                            <th>Anggota</th>
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        const loanTable = $('#transaksi-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.transaksi.peminjaman.data') }}",
                data: function(requestData) {
                    requestData.start_date = $('#start_date').val();
                    requestData.end_date = $('#end_date').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'no_pinjam',
                    name: 'no_pinjam'
                },
                {
                    data: 'tgl_pinjam',
                    name: 'tgl_pinjam'
                },
                {
                    data: 'tgl_kembali',
                    name: 'tgl_kembali'
                },
                {
                    data: 'judul_buku',
                    name: 'judul_buku'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'gambar',
                    name: 'gambar',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'anggota',
                    name: 'anggota'
                },
                {
                    data: 'petugas',
                    name: 'petugas'
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#filter').on('click', function() {
            loanTable.draw();
        });

        loanTable.on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        $(document).on('click', '.kembalikan-buku', function(event) {
            event.preventDefault();
            const form = $(this).closest('form');

            if (!confirm('Yakin ingin mengembalikan buku ini?')) {
                return;
            }

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function() {
                    loanTable.ajax.reload(null, false);
                    toastr.success('Buku berhasil dikembalikan!');
                },
                error: function() {
                    toastr.error('Terjadi kesalahan saat mengembalikan buku.');
                }
            });
        });

        function exportReport(route) {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (!startDate || !endDate) {
                alert('Silakan pilih tanggal awal dan tanggal akhir.');
                return;
            }

            if (endDate < startDate) {
                alert('Tanggal akhir harus sama atau setelah tanggal awal.');
                return;
            }

            window.location.href = route + '?start_date=' + encodeURIComponent(startDate) +
                '&end_date=' + encodeURIComponent(endDate);
        }

        $('#export-pdf').on('click', function() {
            exportReport("{{ route('admin.transaksi.pinjam.exportPdfPinjam') }}");
        });

        $('#export-excel').on('click', function() {
            exportReport("{{ route('admin.transaksi.pinjam.exportExcelPinjam') }}");
        });
    });
</script>
@endpush