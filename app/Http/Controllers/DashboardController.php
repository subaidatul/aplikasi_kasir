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
        // 1. Ambil total Pendapatan, Pengeluaran, dan Laba Bersih
        $totalPendapatan = Pendapatan::sum('total');
        $totalHargaBeli = DetailPendapatan::select(
            DB::raw('SUM(detail_pendapatan.jumlah * barang.harga_beli) as total_beli')
        )
            ->join('barang', 'detail_pendapatan.id_barang', '=', 'barang.id_barang')
            ->value('total_beli') ?? 0;
        $totalPengeluaran = Pengeluaran::sum('total');
        $labaBersih = $totalPendapatan - $totalHargaBeli - $totalPengeluaran;
        
        // 2. Siapkan data untuk grafik bulanan
        $grafikDataBulanan = $this->getChartData('monthly');

        // 3. Siapkan data untuk grafik tahunan
        $grafikDataTahunan = $this->getChartData('yearly');

        // 4. Kirim semua data ke view
        return view('dashboard', compact(
            'totalPendapatan', 
            'totalPengeluaran', 
            'labaBersih', 
            'grafikDataBulanan',
            'grafikDataTahunan'
        ));
    }

    /**
     * Mengambil data pendapatan, pengeluaran, dan pengunjung untuk grafik.
     * @param string $period 'monthly' atau 'yearly'
     * @return array
     */
    private function getChartData(string $period)
    {
        $dateFormat = ($period === 'monthly') ? '%Y-%m' : '%Y';
        $groupByColumn = ($period === 'monthly') ? 'month' : 'year';

        $pendapatanSeries = Pendapatan::select(
            DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as `{$groupByColumn}`"),
            DB::raw('SUM(total) as total')
        )
            ->groupBy($groupByColumn)
            ->orderBy($groupByColumn)
            ->pluck('total', $groupByColumn);

        $pengeluaranSeries = Pengeluaran::select(
            DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as `{$groupByColumn}`"),
            DB::raw('SUM(total) as total') 
        )
            ->groupBy($groupByColumn)
            ->orderBy($groupByColumn)
            ->pluck('total', $groupByColumn);

        $pengunjungSeries = Pendapatan::select(
            DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as `{$groupByColumn}`"),
            DB::raw('COUNT(*) as total_pengunjung')
        )
            ->groupBy($groupByColumn)
            ->orderBy($groupByColumn)
            ->pluck('total_pengunjung', $groupByColumn);

        // Menggabungkan semua periode unik
        $periods = $pendapatanSeries->keys()
                                   ->merge($pengeluaranSeries->keys())
                                   ->merge($pengunjungSeries->keys())
                                   ->unique()
                                   ->sort()
                                   ->values();

        // Mengonversi data ke format yang dibutuhkan oleh Chart.js
        // Perbaikan: Hapus konversi tanggal di sini. Kirim data mentah.
        $labels = $periods->toArray(); 
        $pendapatanData = $periods->map(fn($p) => $pendapatanSeries->get($p, 0))->toArray();
        $pengeluaranData = $periods->map(fn($p) => $pengeluaranSeries->get($p, 0))->toArray();
        $pengunjungData = $periods->map(fn($p) => $pengunjungSeries->get($p, 0))->toArray();
        
        return [
            'labels' => $labels,
            'pendapatan' => $pendapatanData,
            'pengeluaran' => $pengeluaranData,
            'pengunjung' => $pengunjungData
        ];
    }
}