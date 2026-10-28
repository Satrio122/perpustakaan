<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('admin.user.index', compact('users'));
    }

    public function store(Request $request)
    {
        $messages = [
            'image.image' => 'Field image harus berupa file gambar.',
            'image.mimes' => 'Field image hanya boleh berupa file dengan format: jpeg, jpg, atau png.',
            'image.max' => 'Ukuran file pada field image tidak boleh lebih dari 1 MB.',
            'email.required' => 'Field email wajib diisi.',
            'email.email' => 'Field email harus berisi alamat email yang valid.',
            'email.max' => 'Field email tidak boleh lebih dari 128 karakter.',
            'email.unique' => 'Field email sudah terdaftar. Silakan gunakan email lain.',
        ];

        $request->validate([
            'nama' => ['required', 'string', 'max:128'],
            'alamat' => ['required', 'string'],
            'email' => ['required', 'email', 'max:128', 'unique:users'],
            'role_id' => ['required', 'integer', 'in:1,2'],
            'is_active' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
        ], $messages);

        // Upload dan simpan gambar
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profil-pic', 'public');
        } else {
            $imagePath = 'profil-pic/default.jpg';
        }

        // Simpan data ke dalam database
        $data = $request->only([
            'nama',
            'alamat',
            'email',
            'role_id',
            'is_active',
        ]);

        $data['image'] = $imagePath;
        $data['password'] = Hash::make('password');

        User::create($data);

        return redirect()->back()->with(
            'success',
            'User berhasil ditambahkan!'
        );
    }

    public function show(User $user)
    {
        return json_encode($user);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required|string|max:128',
            'alamat' => 'required|string',
            'email' => 'required|email|max:128|unique:users,email,' . $user->id,
            'role_id' => 'required|integer|in:1,2',
            'is_active' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:1024',
        ]);

        $data = $request->only([
            'nama',
            'alamat',
            'email',
            'role_id',
            'is_active',
        ]);

        if ($request->hasFile('image')) {
            if ($user->image && $user->image !== 'profil-pic/default.jpg') {
                Storage::disk('public')->delete($user->image);
            }

            $data['image'] = $request->file('image')->store('profil-pic', 'public');
        }

        $user->update($data);

        return redirect()->back()->with(
            'success',
            'User berhasil diubah.'
        );
    }

    public function destroy(User $user)
    {
        if (
            $user->image &&
            $user->image != 'profil-pic/default.jpg'
        ) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return redirect()->back()->with(
            'success',
            'User berhasil dihapus.'
        );
    }

    public function resetPassword(User $user)
    {
        User::where('id', $user->id)->update([
            'password' => Hash::make('password'),
        ]);

        return redirect()->route('admin.master.user.index')->with(
            'success',
            'Password berhasil direset'
        );
    }
}
