<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index()
    {
        $buku = Buku::with('kategori')->paginate(12);
        return view('member.index', compact('buku'));
    }

    public function detailBuku(Buku $buku)
    {
        $detailBuku = Buku::with('kategori')->find($buku->id);
        return response()->json($detailBuku);
    }
    public function tampilProfil()
    {
        $user = Auth::user();
        $profileImage = $user->image && Storage::disk('public')->exists($user->image)
            ? asset('storage/' . $user->image)
            : asset('assets/dist/img/user2-160x160.jpg');

        return view('member.profil', compact('profileImage'));
    }
    public function updateProfil(Request $request)
    {
        $messages = [
            'nama.required' => 'Nama lengkap harus diisi.',
            'alamat.required' => 'Alamat harus diisi.',
            'image.image' => 'Gambar harus berupa file image.',
            'image.max' => 'Maksimal ukuran file 1 MB.',
        ];

        $validatedData = $request->validate([
            'nama' => ['required', 'string', 'max:128'],
            'alamat' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
        ], $messages);

        $user = Auth::user();

        if ($request->hasFile('image')) {
            if ($user->image && $user->image !== 'profile-pic/default.jpg') {
                Storage::disk('public')->delete($user->image);
            }

            $validatedData['image'] = $request->file('image')->store('profile-pic', 'public');
        }

        User::where('id', $user->id)->update($validatedData);

        return redirect()->route('member.profil')->with('success', 'Profil berhasil diperbarui.');
    }

    public function tampilGantiPassword()
    {
        return view('member.ganti_password');
    }

    public function updateGantiPassword(Request $request)
    {
        $messages = [
            'password_saat_ini.required' => 'Password saat ini harus diisi.',
            'password_saat_ini.min' => 'Minimal 8 karakter.',
            'password_baru.required' => 'Password baru harus diisi.',
            'password_baru.min' => 'Minimal 8 karakter.',
            'konfirmasi_password.required' => 'Konfirmasi password harus diisi.',
            'konfirmasi_password.min' => 'Minimal 8 karakter.',
            'konfirmasi_password.same' => 'Password dan konfirmasi password tidak cocok.',
        ];

        $validatedData = $request->validate([
            'password_saat_ini' => ['required', 'string', 'min:8'],
            'password_baru' => ['required', 'string', 'min:8'],
            'konfirmasi_password' => ['required', 'string', 'min:8', 'same:password_baru'],
        ], $messages);

        $user = Auth::user();

        if (!Hash::check($validatedData['password_saat_ini'], $user->password)) {
            return back()->with('error', 'Gagal, password saat ini salah.');
        }

        User::where('id', $user->id)->update([
            'password' => Hash::make($validatedData['password_baru']),
        ]);

        return redirect()->route('member.ganti-password')->with('success', 'Password berhasil diupdate.');
    }
}
