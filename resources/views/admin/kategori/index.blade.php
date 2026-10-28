@extends('admin.layout.main')

@section('title', 'Master Kategori')

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-12">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Tambah Kategori</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#modalTambah">
                            <i class="fas fa-plus"></i> Tambah Kategori
                        </button>
                    </div>

                </div>

                <div class="card-body">

                    <div class="row">

                        @foreach ($kategori as $index => $item)

                        @php
                        $bgArray = ['primary', 'secondary', 'success', 'info', 'danger', 'warning'];
                        $bg_random = $bgArray[$index % count($bgArray)];
                        @endphp

                        <div class="col-lg-3 col-md-4 col-sm-6">

                            <div class="small-box bg-{{ $bg_random }}">

                                <div class="inner">

                                    <h3>{{ $loop->iteration }}</h3>

                                    <p>
                                        {{ $item->nama_kategori }}
                                        {{ $item->buku_count }} Buku
                                    </p>

                                </div>

                                <div class="icon">
                                    <i class="fas fa-book"></i>
                                </div>

                                <a href="#modalEdit" class="small-box-footer btn-edit-kategori"
                                    data-toggle="modal"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_kategori }}"
                                    data-action="{{ route('admin.master.kategori.update', $item->id) }}"
                                    data-delete-action="{{ route('admin.master.kategori.destroy', $item->id) }}">
                                    Ubah <i class="fas fa-edit"></i>
                                </a>

                            </div>

                        </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>

<!-- Modal Tambah Kategori -->

<div class="modal fade" id="modalTambah">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title">Tambah Kategori Baru</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <form action="{{ route('admin.master.kategori.store') }}" method="POST">

                @csrf

                <div class="modal-body">

                    <div class="form-group">

                        <label for="nama_kategori">Nama Kategori</label>

                        <input type="text"
                            name="nama_kategori"
                            id="nama_kategori"
                            class="form-control"
                            placeholder="Masukkan nama kategori"
                            required>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Tutup
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- Modal Ubah Kategori -->

<div class="modal fade" id="modalEdit">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title">Ubah Kategori</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <form id="formEditKategori" method="POST">

                @csrf
                @method('PUT')

                <div class="modal-body">

                    <div class="form-group">

                        <label for="edit_nama_kategori">Nama Kategori</label>

                        <input type="text"
                            name="nama_kategori"
                            id="edit_nama_kategori"
                            class="form-control"
                            required>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Tutup
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Ubah
                    </button>

                    <button type="button" class="btn btn-danger" id="btnHapusKategori">
                        Hapus
                    </button>

                </div>

            </form>

            <form id="formHapusKategori" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
    $(function() {
        $('.btn-edit-kategori').on('click', function() {
            $('#edit_nama_kategori').val($(this).data('nama'));
            $('#formEditKategori').attr('action', $(this).data('action'));
            $('#formHapusKategori').attr('action', $(this).data('delete-action'));
        });

        $('#btnHapusKategori').on('click', function() {
            if (confirm('Hapus kategori ini?')) {
                $('#formHapusKategori').submit();
            }
        });
    });
</script>
@endpush