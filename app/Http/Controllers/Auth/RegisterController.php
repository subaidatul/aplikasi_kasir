<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function registerForm()
    {
        return view('administrator.auth.register');
    }

    public function register(Request $request)
    {
        // Validasi input pengguna, termasuk role
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email'    => 'required|string|email|max:255|unique:users',
            'role'     => 'required|string', // Validasi input role
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Buat pengguna baru dengan role yang dipilih
        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role, // Simpan role yang dipilih dari form
        ]);

        return redirect()->route('login.form')->with('success', 'Registrasi berhasil! Silakan masuk.');
    }
}