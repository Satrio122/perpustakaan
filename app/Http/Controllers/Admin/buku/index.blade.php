@extends('admin.layout.main')

@section('title', 'Master Buku')

@section('content')

<div class="container-fluid">

    <div class="row mb-3">

        <div class="col-12">

            <a href="{{ route('admin.master.buku.create') }}"
                class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Buku
            </a>

        </div>

    </div>

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Data Buku</h3>
                </div>

                <div class="card-body">

                    <table id="tableBuku"
                        class="table table-bordered table-striped">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul Buku</th>
                                <th>Kategori</th>
                                <th>Pengarang</th>
                                <th>Penerbit</th>
                                <th>Tahun Terbit</th>
                                <th>Gambar</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($buku as $item)

                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $item->judul_buku }}</td>

                                <td>
                                    {{ $item->kategori->nama_kategori }}
                                </td>

                                <td>{{ $item->pengarang }}</td>

                                <td>{{ $item->penerbit }}</td>

                                <td>{{ $item->tahun_terbit }}</td>

                                <td>
                                    <img src="{{ asset('storage/' . $item->image) }}"
                                        alt="Cover Buku"
                                        width="50"
                                        height="70"
                                        style="object-fit: cover;">
                                </td>

                                <td>

                                    <a href="{{ route('admin.master.buku.edit', $item->id) }}"
                                        class="btn btn-xs btn-warning"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Edit">

                                        <i class="fas fa-edit"></i>

                                    </a>

                                    <div class="d-inline">

                                        <form action="{{ route('admin.master.buku.destroy', $item->id) }}"
                                            method="POST"
                                            class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="button"
                                                class="btn btn-xs btn-danger hapus-data"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                title="Hapus">

                                                <i class="fas fa-trash-alt"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                        <tfoot>

                            <tr>
                                <th>#</th>
                                <th>Judul Buku</th>
                                <th>Kategori</th>
                                <th>Pengarang</th>
                                <th>Penerbit</th>
                                <th>Tahun Terbit</th>
                                <th>Gambar</th>
                                <th>Aksi</th>
                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>
    $(document).ready(function() {

        $('#tableBuku').DataTable({
            responsive: true,
            autoWidth: false
        });

    });
</script>

@endpush