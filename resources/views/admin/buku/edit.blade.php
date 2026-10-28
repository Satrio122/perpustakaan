@extends('admin.layout.main')

@section('title', 'Master Buku')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Buku</h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.master.buku.update', $buku->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="judul_buku">Judul Buku</label>
                                    <input type="text" name="judul_buku" id="judul_buku" class="form-control" value="{{ old('judul_buku', $buku->judul_buku) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="isbn">ISBN</label>
                                    <input type="text" name="isbn" id="isbn" class="form-control" value="{{ old('isbn', $buku->isbn) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_kategori">Kategori Buku</label>
                                    <select name="id_kategori" id="id_kategori" class="form-control" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($kategori as $item)
                                        <option value="{{ $item->id }}" {{ old('id_kategori', $buku->id_kategori) == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama_kategori }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tahun_terbit">Tahun Terbit</label>
                                    <input type="number" name="tahun_terbit" id="tahun_terbit" class="form-control" min="1900" max="2100" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pengarang">Pengarang</label>
                                    <input type="text" name="pengarang" id="pengarang" class="form-control" value="{{ old('pengarang', $buku->pengarang) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stok">Stok</label>
                                    <input type="number" name="stok" id="stok" class="form-control" min="0" value="{{ old('stok', $buku->stok) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="penerbit">Penerbit</label>
                                    <input type="text" name="penerbit" id="penerbit" class="form-control" value="{{ old('penerbit', $buku->penerbit) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dipinjam">Dipinjam</label>
                                    <input type="number" name="dipinjam" id="dipinjam" class="form-control" value="{{ old('dipinjam', $buku->dipinjam) }}" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dibooking">Dibooking</label>
                                    <input type="number" name="dibooking" id="dibooking" class="form-control" value="{{ old('dibooking', $buku->dibooking) }}" min="0" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">Gambar</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="image" id="image" class="custom-file-input" onchange="previewImage()">
                                            <label class="custom-file-label" for="image">Pilih file...</label>
                                        </div>
                                    </div>
                                    @if ($buku->image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $buku->image) }}" class="img-thumbnail" style="max-width: 180px;">
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Kembali</button>
                                <button type="submit" class="btn btn-primary float-right">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function previewImage() {
        const input = document.getElementById('image');
        const label = document.querySelector('.custom-file-label');

        if (input.files && input.files[0]) {
            label.textContent = input.files[0].name;
        }
    }
</script>
@endpush