@extends('admin.layout.main')

@section('title', 'Transaksi Booking')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Booking</h3>
                </div>

                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID Booking</th>
                                <th>Tanggal Booking</th>
                                <th>Batas Ambil</th>
                                <th>Anggota</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($booking as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id_booking }}</td>
                                <td>{{ $item->tgl_booking }}</td>
                                <td>{{ $item->batas_ambil }}</td>
                                <td>{{ $item->anggota->nama }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.transaksi.booking.show', $item->id) }}"
                                        class="btn btn-info btn-sm" data-toggle="tooltip" title="Lihat booking">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.transaksi.booking.destroy', $item->id) }}"
                                        method="POST" class="d-inline hapus-data">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            data-toggle="tooltip" title="Hapus booking">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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

        $(document).on('submit', '.hapus-data', function(event) {
            if (!confirm('Yakin ingin menghapus data?')) {
                event.preventDefault();
            }
        });
    });
</script>
@endpush