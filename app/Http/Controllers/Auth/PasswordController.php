<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    /**
     * Tampilkan formulir permintaan tautan reset kata sandi.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('administrator.auth.forgot-password');
    }

    /**
     * Kirim tautan reset kata sandi melalui email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? back()->with('status', 'Kami telah mengirimkan tautan reset kata sandi ke alamat email Anda.')
            : back()->withErrors(['email' => trans($response)]);
    }

    /**
     * Tampilkan formulir reset kata sandi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request)
    {
        return view('administrator.auth.reset-password')->with(
            ['token' => $request->token, 'email' => $request->email]
        );
    }

    /**
     * Reset kata sandi pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $response = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $response == Password::PASSWORD_RESET
            ? redirect()->route('login.form')->with('success', 'Kata sandi Anda telah direset. Silakan masuk dengan kata sandi baru.')
            : back()->withErrors(['email' => trans($response)]);
    }
}
