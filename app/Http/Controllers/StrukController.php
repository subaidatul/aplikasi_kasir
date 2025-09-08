<?php

namespace App\Http\Controllers;

use App\Models\Pendapatan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class StrukController extends Controller
{
    /**
     * Menampilkan daftar semua struk pendapatan dan pengeluaran.
     */
    public function index()
    {
        // Ambil semua data pendapatan dan pengeluaran
        $pendapatan = Pendapatan::with('unit')->get();
        $pengeluaran = Pengeluaran::get();

        // Gabungkan kedua koleksi dan urutkan berdasarkan tanggal
        $struks = $pendapatan->toBase()->merge($pengeluaran->toBase())->sortByDesc('created_at');

        // Kirim data ke view
        return view('struk.index', compact('struks'));
    }

    /**
     * Menampilkan detail struk berdasarkan jenis dan ID.
     */
    public function show($jenis, $id)
    {
        $data = null;
        if ($jenis == 'pendapatan') {
            $data = Pendapatan::with('unit', 'detailPendapatan.barang')->find($id);
            if (!$data) {
                abort(404, 'Transaksi pendapatan tidak ditemukan.');
            }
        } elseif ($jenis == 'pengeluaran') {
            // Perubahan: Hapus with('unit') dari sini
            $data = Pengeluaran::with('details')->find($id);
            if (!$data) {
                abort(404, 'Transaksi pengeluaran tidak ditemukan.');
            }
        } else {
            abort(404, 'Jenis transaksi tidak valid.');
        }

        return view('struk.show', compact('data', 'jenis'));
    }
}