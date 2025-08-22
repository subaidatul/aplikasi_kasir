<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Menampilkan formulir registrasi.
     */
    public function registerForm()
    {
        return view('administrator.auth.register');
    }

    /**
     * Menangani proses registrasi.
     */
    public function register(Request $request)
    {
        // Validasi input pengguna
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Buat pengguna baru, tambahkan 'username' dari 'name'
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'username' => $request->name, 
            'password' => Hash::make($request->password),
        ]);

        // Redirect pengguna ke halaman login setelah registrasi
        return redirect()->route('login.form')->with('success', 'Registrasi berhasil! Silakan masuk.');
    }
}
