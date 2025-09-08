<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        // Ambil semua akun pengguna, termasuk admin
        $accounts = User::all(); // Cukup ganti baris ini
        return view('administrator.accounts.index', compact('accounts'));
    }

    public function destroy(User $account)
    {
        // Pastikan admin tidak bisa menghapus akunnya sendiri
        if ($account->id_user === Auth::user()->id_user) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $account->delete();
        return back()->with('success', 'Akun berhasil dihapus.');
    }
}