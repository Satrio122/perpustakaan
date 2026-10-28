<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::withCount('buku')->get(); // menampilkan semua kategori dan berapa jumlah buku untuk masing-masing kategorinya

        return view('admin.kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => ['required', 'string', 'max:30'],
        ]);

        // Cek apakah nama kategori sudah ada
        $kategoriExists = Kategori::where('nama_kategori', $request->nama_kategori)->exists();

        if ($kategoriExists) {
            return redirect()->back()->with('error', 'Nama kategori sudah ada!');
        }

        // Jika tidak ada, simpan data
        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_kategori' => ['required', 'string', 'max:30'],
        ]);

        // Cek apakah nama kategori sudah ada
        $kategoriExists = Kategori::where('nama_kategori', $request->nama_kategori)
            ->where('id', '!=', $id)
            ->exists();

        if ($kategoriExists) {
            return redirect()->back()->with('error', 'Nama kategori sudah ada!');
        }

        Kategori::where('id', $id)->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy(string $id)
    {
        $post = Kategori::findOrFail($id);

        // delete post
        $post->delete();

        // redirect to index
        return redirect()->back()->with([
            'success' => 'Kategori berhasil dihapus!'
        ]);
    }
}
