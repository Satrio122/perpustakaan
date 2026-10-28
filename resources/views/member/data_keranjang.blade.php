@extends('member.layout.main')

@section('title', 'Data Keranjang')

@section('content')
<main class="container pt-4">
    <div class="mb-3">
        <a href="{{ route('member.index') }}" class="btn btn-outline-primary">Lanjutkan melihat katalog</a>
        <form action="{{ route('member.simpanBooking') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="id" value="{{ Auth::id() }}">
            <button type="submit" class="btn btn-success">Selesai Booking</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judul Buku</th>
                    <th>Kategori</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th>Tahun Terbit</th>
                    <th>Stok</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data_keranjang as $item)
                <tr class="{{ $item->buku && $item->buku->stok == 0 ? 'bg-danger' : '' }}">
                    <td>{{ $loop->iteration }}</td>
                    @if ($item->buku)
                    <td>{{ $item->buku->judul_buku }}</td>
                    <td>{{ $item->buku->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ $item->buku->pengarang }}</td>
                    <td>{{ $item->buku->penerbit }}</td>
                    <td>{{ $item->buku->tahun_terbit }}</td>
                    <td>{{ $item->buku->stok }}</td>
                    <td class="text-center">
                        <img src="{{ asset('storage/' . $item->buku->image) }}" alt="Cover {{ $item->buku->judul_buku }}" style="height: 100px; width: 70px; object-fit: cover;">
                    </td>
                    @else
                    <td colspan="7">Data buku sudah tidak tersedia.</td>
                    @endif
                    <td>
                        @if ($item->buku)
                        <form action="{{ route('member.hapusKeranjang', ['buku' => $item->buku->id, 'user' => Auth::id()]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus buku ini dari keranjang?')" aria-label="Hapus dari keranjang">
                                <i class="fas fa-trash-alt" aria-hidden="true"></i> Hapus
                            </button>
                        </form>
                        @else
                        <form action="{{ route('member.hapusKeranjangId', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" aria-label="Hapus data buku dari keranjang">
                                <i class="fas fa-trash-alt" aria-hidden="true"></i> Hapus
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>
@endsection