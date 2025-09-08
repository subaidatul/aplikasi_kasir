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
        $totalPendapatan = Pendapatan::sum('total');

        // 2. Hitung total Harga Beli dari semua barang yang terjual
        $totalHargaBeli = DetailPendapatan::select(
            DB::raw('SUM(detail_pendapatan.jumlah * barang.harga_beli) as total_beli')
        )
            ->join('barang', 'detail_pendapatan.id_barang', '=', 'barang.id_barang')
            ->value('total_beli') ?? 0;

        // 3. Ambil total Pengeluaran
        $totalPengeluaran = Pengeluaran::sum('total');

        // Laba Kotor dihapus, karena tidak dibutuhkan

        // 5. Hitung Laba Bersih
        // Laba Bersih = Harga Jual - Harga Beli - Pengeluaran
        $labaBersih = $totalPendapatan - $totalHargaBeli - $totalPengeluaran;
        
        // 6. Siapkan data untuk grafik PERBULAN
        $pendapatanSeries = Pendapatan::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(total) as total')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $pengeluaranSeries = Pengeluaran::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(total) as total') 
        )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Menambahkan query untuk menghitung jumlah pengunjung (berdasarkan jumlah transaksi)
        $pengunjungSeries = Pendapatan::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total_pengunjung')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_pengunjung', 'month');

        // Menggabungkan semua bulan unik dari ketiga series
        $months = $pendapatanSeries->keys()
                                   ->merge($pengeluaranSeries->keys())
                                   ->merge($pengunjungSeries->keys())
                                   ->unique()
                                   ->sort()
                                   ->values();

        // Mengonversi format bulan ke label yang lebih mudah dibaca
        $labels = $months->map(fn($month) => date('M Y', strtotime($month . '-01')))->toArray();
        $pendapatanData = $months->map(fn($month) => $pendapatanSeries->get($month, 0))->toArray();
        $pengeluaranData = $months->map(fn($month) => $pengeluaranSeries->get($month, 0))->toArray();
        $pengunjungData = $months->map(fn($month) => $pengunjungSeries->get($month, 0))->toArray();
        
        $grafikData = [
            'labels' => $labels,
            'pendapatan' => $pendapatanData,
            'pengeluaran' => $pengeluaranData,
            'pengunjung' => $pengunjungData
        ];

        // 7. Kirim data ke view. Perhatikan variabel 'labaKotor' sudah dihapus.
        return view('dashboard', compact('totalPendapatan', 'totalPengeluaran', 'labaBersih', 'grafikData'));
    }
}