@extends('member.layout.main')

@section('title', 'Keranjang')

@section('content')
<main class="container pt-4">
    @if ($keranjang->isEmpty())
    <div class="alert alert-info">Keranjang masih kosong.</div>
    <a href="{{ route('member.index') }}" class="btn btn-primary">Kembali ke katalog</a>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="thead-light">
                <tr>
                    <th scope="col">Buku</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Pengarang</th>
                    <th scope="col">Stok</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($keranjang as $item)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('storage/' . $item->buku->image) }}" alt="{{ $item->buku->judul_buku }}" class="mr-3" style="width: 48px; height: 64px; object-fit: cover;">
                            <span>{{ $item->buku->judul_buku }}</span>
                        </div>
                    </td>
                    <td>{{ $item->buku->kategori->nama_kategori }}</td>
                    <td>{{ $item->buku->pengarang }}</td>
                    <td>{{ $item->buku->stok }}</td>
                    <td>
                        <form action="{{ route('member.hapusKeranjangId', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Hapus {{ $item->buku->judul_buku }} dari keranjang">
                                <i class="fas fa-trash" aria-hidden="true"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <a href="{{ route('member.index') }}" class="btn btn-secondary">Lanjut melihat katalog</a>
    @endif
</main>
@endsection