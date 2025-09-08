<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  array  $roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan pengguna sudah login terlebih dahulu
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }

        $user = Auth::user();

        // Periksa apakah peran pengguna ada di dalam daftar peran yang diizinkan
        if (!in_array($user->role, $roles)) {
            // Jika peran tidak sesuai, kembalikan ke halaman sebelumnya atau halaman lain
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $next($request);
    }
}