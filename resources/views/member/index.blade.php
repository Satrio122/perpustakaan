@extends('member.layout.main')

@section('title', 'Katalog Buku')

@section('content')

<main role="main" class="container pt-4">
    <div class="row">
        @foreach ($buku as $item)
        <div class="col-lg-3 col-md-4 col-sm-6 d-flex align-items-stretch">
            <div class="card" style="width: 100%;">
                <div class="ribbon-wrapper ribbon-lg">
                    <div class="ribbon {{ $item->stok > 0 ? 'bg-success' : 'bg-danger' }}">
                        {{ $item->stok > 0 ? 'Stok: ' . $item->stok : 'Tidak Tersedia' }}
                    </div>
                </div>

                <div class="card-header d-flex justify-content-center" style="background-color: lightgrey">
                    <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->judul_buku }}" style="max-width: 120px;">
                </div>

                <div class="card-body text-center pb-1">
                    <p class="card-text text-bold">{{ $item->judul_buku }}</p>
                    <h6 class="text-center">
                        <span class="badge badge-pill badge-info">{{ $item->kategori?->nama_kategori ?? 'Tanpa kategori' }}</span>
                    </h6>
                    <p class="card-text text-center text-primary mb-0">{{ $item->pengarang }}</p>
                    <p class="card-text text-center text-danger mb-0">{{ $item->penerbit }}</p>
                    <p class="card-text text-center text-success mb-0">{{ $item->tahun_terbit }}</p>
                </div>

                <div class="card-footer">
                    <button class="btn btn-sm btn-primary" data-detail-url="{{ route('member.detailBuku', $item->id) }}" onclick="detailBuku(this.dataset.detailUrl);">Detail</button>
                    @if ($item->stok > 0)
                    <form action="{{ route('member.tambahKeranjang') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="id" value="{{ $item->id }}">
                        <button type="submit" class="btn btn-sm btn-success">Tambah ke keranjang</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{ $buku->links() }}
</main>

<!-- Modal Detail Buku -->
<div class="modal fade" id="detailBukuModal" tabindex="-1" role="dialog" aria-labelledby="detailBukuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="detailBukuModalLabel">Detail Buku</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-3 d-flex justify-content-center align-items-center" style="border-right: 1px solid black">
                        <img src="" class="card-img-top" alt="Cover Buku" style="max-width: 120px;" id="gambar">
                    </div>
                    <div class="col-lg-9 pl-2">
                        <dl>
                            <dt>Judul Buku</dt>
                            <dd id="judul_buku"></dd>
                            <dt>Kategori</dt>
                            <dd id="kategori"></dd>
                            <dt>Pengarang</dt>
                            <dd id="pengarang"></dd>
                            <dt>Penerbit</dt>
                            <dd id="penerbit"></dd>
                            <dt>Tahun Terbit</dt>
                            <dd id="tahun_terbit"></dd>
                            <dt>ISBN</dt>
                            <dd id="isbn"></dd>
                            <dt>Stok</dt>
                            <dd id="stok"></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <form action="{{ route('member.tambahKeranjang') }}" method="POST" id="tambahKeranjangForm" class="d-inline">
                    @csrf
                    <input type="hidden" name="id" id="tambahKeranjangId">
                    <button type="submit" class="btn btn-success" id="tambahKeranjang">Tambah ke keranjang</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Login -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('loginMember') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="loginModalLabel">Login Member</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="login_email">Email</label>
                        <input type="email" class="form-control" id="login_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="login_password">Password</label>
                        <input type="password" class="form-control" id="login_password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <p>Belum punya akun? <span class="text-primary" style="cursor: pointer;" onclick="daftarModal();">Daftar di sini</span></p>
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Daftar Member -->
<div class="modal fade" id="daftarMemberModal" tabindex="-1" role="dialog" aria-labelledby="daftarMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="daftarMemberForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="daftarMemberModalLabel">Daftar Member</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama lengkap" required>
                                <span class="invalid-feedback" role="alert"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <input type="text" name="alamat" class="form-control" id="alamat" placeholder="Alamat" required>
                                <span class="invalid-feedback" role="alert"></span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                                <span class="invalid-feedback" role="alert"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                                <span class="invalid-feedback" role="alert"></span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label for="konfirmasi_password">Konfirmasi Password</label>
                                <input type="password" name="konfirmasi_password" class="form-control" id="konfirmasi_password" placeholder="Konfirmasi Password" required>
                                <span class="invalid-feedback" role="alert"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <p>Sudah punya akun? <span class="text-primary" style="cursor: pointer;" onclick="loginModal();">Login di sini</span></p>
                    <button type="submit" class="btn btn-primary">Daftar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function detailBuku(url) {
        $.ajax({
            url: url,
            dataType: 'json',
            type: 'GET',
            error: function() {
                toastr.error('Server error occurred', 'Error', {
                    closeButton: true,
                    progressBar: true
                });
            },
            success: function(data) {
                $('#gambar').attr('src', new URL('storage/' + data.image, window.location.href).toString());
                $('#judul_buku').text(data.judul_buku);
                $('#kategori').text(data.kategori ? data.kategori.nama_kategori : 'Tanpa kategori');
                $('#pengarang').text(data.pengarang);
                $('#penerbit').text(data.penerbit);
                $('#tahun_terbit').text(data.tahun_terbit);
                $('#isbn').text(data.isbn);
                $('#stok').text(data.stok);
                $('#tambahKeranjangId').val(data.id);
                $('#tambahKeranjang').toggleClass('d-none', data.stok <= 0);
            }
        });

        $('#detailBukuModal').modal('show');
    }

    function daftarModal() {
        $('#daftarMemberModal').modal('show');
        $('#loginModal').modal('hide');
    }

    function loginModal() {
        $('#daftarMemberModal').modal('hide');
        $('#loginModal').modal('show');
    }

    $('#daftarMemberForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').empty();

        $.ajax({
            url: "{{ route('registerMember') }}",
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.success);
                    $('#daftarMemberModal').modal('hide');
                    form[0].reset();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key).next('.invalid-feedback').text(value[0]);
                    });
                    return;
                }

                toastr.error('Pendaftaran gagal. Silakan coba kembali.');
            }
        });
    });
</script>
@endpush