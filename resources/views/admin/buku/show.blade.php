@extends('admin.layout.main')

@section('title', 'Master Buku')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Data Buku</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Judul Buku</label>
                                        <input type="text" class="form-control" value="{{ $buku->judul_buku }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ISBN</label>
                                        <input type="text" class="form-control" value="{{ $buku->isbn }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kategori Buku</label>
                                        <input type="text" class="form-control" value="{{ $buku->kategori->nama_kategori ?? '-' }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tahun Terbit</label>
                                        <input type="text" class="form-control" value="{{ $buku->tahun_terbit }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pengarang</label>
                                        <input type="text" class="form-control" value="{{ $buku->pengarang }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Stok</label>
                                        <input type="text" class="form-control" value="{{ $buku->stok }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Dipinjam</label>
                                        <input type="text" class="form-control" value="{{ $buku->dipinjam }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Penerbit</label>
                                        <input type="text" class="form-control" value="{{ $buku->penerbit }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Dibooking</label>
                                        <input type="text" class="form-control" value="{{ $buku->dibooking }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 text-center">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="border rounded p-2 bg-light" style="height: 220px; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ asset('storage/' . $buku->image) }}"
                                        alt="Cover Buku"
                                        style="max-width: 100%; max-height: 180px; object-fit: cover; border-radius: 4px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-left">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">Kembali</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection