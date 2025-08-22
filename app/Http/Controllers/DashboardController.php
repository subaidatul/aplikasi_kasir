<?php

namespace App\Http\Controllers;

use App\Models\Pendapatan;
use App\Models\Pengeluaran;
use App\Models\DetailPendapatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil total Harga Jual (Pendapatan)
        $totalPendapatan = Pendapatan::sum('total'); // Perbaikan: Ubah nama variabel di sini

        // 2. Hitung total Harga Beli dari semua barang yang terjual
        $totalHargaBeli = DetailPendapatan::select(
            DB::raw('SUM(detail_pendapatan.jumlah * barang.harga_beli) as total_beli')
        )
            ->join('barang', 'detail_pendapatan.id_barang', '=', 'barang.id_barang')
            ->value('total_beli') ?? 0;

        // 3. Ambil total Pengeluaran
        $totalPengeluaran = Pengeluaran::sum('total');

        // 4. Hitung Laba Kotor
        // Laba Kotor = Harga Jual - Harga Beli
        $labaKotor = $totalPendapatan - $totalHargaBeli; // Perbaikan: Gunakan variabel yang baru
        
        // 5. Hitung Laba Bersih
        // Laba Bersih = Harga Jual - Harga Beli - Pengeluaran
        $labaBersih = $totalPendapatan - $totalHargaBeli - $totalPengeluaran; // Perbaikan: Gunakan variabel yang baru
        
        // 6. Siapkan data untuk grafik
        $pendapatanSeries = Pendapatan::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as total')
        )
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $pengeluaranSeries = Pengeluaran::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as total') 
        )
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $dates = $pendapatanSeries->keys()->merge($pengeluaranSeries->keys())->unique()->sort()->values();

        $labels = $dates->map(fn($date) => date('d M', strtotime($date)))->toArray();
        $pendapatanData = $dates->map(fn($date) => $pendapatanSeries->get($date, 0))->toArray();
        $pengeluaranData = $dates->map(fn($date) => $pengeluaranSeries->get($date, 0))->toArray();
        
        $grafikData = [
            'labels' => $labels,
            'pendapatan' => $pendapatanData,
            'pengeluaran' => $pengeluaranData
        ];

        // 7. Kirim data ke view. Pastikan variabel yang dikirim sama dengan di view.
        return view('dashboard', compact('totalPendapatan', 'totalPengeluaran', 'labaKotor', 'labaBersih', 'grafikData')); // Perbaikan: Ganti 'totalHargaJual' menjadi 'totalPendapatan'
    }
}