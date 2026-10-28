<?php

namespace App\Http\Controllers\Admin;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BukuController extends Controller
{
    public function index()
    {
        $buku = Buku::with('kategori')->get();
        $kategori = Kategori::all();
        return view('admin.buku.index', compact('buku', 'kategori'));
    }

    public function create()
    {
        $kategori = Kategori::all();
        return view('admin.buku.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $messages = [
            'judul_buku.required' => 'Judul buku wajib diisi.',
            'id_kategori.required' => 'Kategori wajib dipilih.',
            'id_kategori.exists' => 'Kategori tidak valid.',
            'pengarang.required' => 'Pengarang wajib diisi.',
            'penerbit.required' => 'Penerbit wajib diisi.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.digits' => 'Tahun terbit harus 4 digit.',
            'isbn.required' => 'ISBN wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok minimal 0.',
            'image.image' => 'Gambar harus berupa file image.',
            'image.mimes' => 'Format gambar harus jpg, jpeg, atau png.',
            'image.max' => 'Ukuran gambar maksimal 1 MB.',
        ];

        $validated = $request->validate([
            'judul_buku' => 'required|string|max:128',
            'id_kategori' => 'required|exists:kategori,id',
            'pengarang' => 'required|string|max:64',
            'penerbit' => 'required|string|max:64',
            'tahun_terbit' => 'required|digits:4',
            'isbn' => 'required|string|max:64',
            'stok' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:1024',
        ], $messages);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('cover-buku', 'public');
        } else {
            $validated['image'] = 'cover-buku/book-defaultcover.jpg';
        }

        $validated['dipinjam'] = 0;
        $validated['dibooking'] = 0;

        Buku::create($validated);

        return redirect()
            ->route('admin.master.buku.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    public function show(Buku $buku)
    {
        $buku->load('kategori');
        return view('admin.buku.show', compact('buku'));
    }

    public function edit(Buku $buku)
    {
        $kategori = Kategori::all();
        return view('admin.buku.edit', compact('buku', 'kategori'));
    }

    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'judul_buku' => 'required|string|max:128',
            'id_kategori' => 'required|exists:kategori,id',
            'pengarang' => 'required|string|max:64',
            'penerbit' => 'required|string|max:64',
            'tahun_terbit' => 'required|digits:4',
            'isbn' => 'required|string|max:64',
            'stok' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:1024',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('cover-buku', 'public');
        }

        $buku->update($validated);

        return redirect()->route('admin.master.buku.index')->with('success', 'Data buku berhasil diperbarui');
    }

    public function destroy(Buku $buku)
    {
        $buku->delete();

        return redirect()->route('admin.master.buku.index')->with('success', 'Data buku berhasil dihapus');
    }
}
