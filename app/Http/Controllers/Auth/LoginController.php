<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan formulir login.
     */
    public function loginForm()
    {
        return view('administrator.auth.login');
    }

    /**
     * Menangani proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'login' => 'Nama pengguna atau email dan password yang Anda masukkan tidak cocok dengan catatan kami.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }
}
