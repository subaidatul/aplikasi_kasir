<?php

namespace App\Http\Controllers;

use App\Models\Pendapatan;
use App\Models\Pengeluaran;
use App\Models\DetailPendapatan;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil semua unit untuk opsi filter
        $units = Unit::all();

        // 2. Ambil filter dari request
        $idUnit = $request->input('id_unit');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        // 3. Bangun query dasar
        $queryPendapatan = Pendapatan::query();
        $queryPengeluaran = Pengeluaran::query();
        $queryDetailPendapatan = DetailPendapatan::query();
        
        // 4. Terapkan filter ke semua query jika ada
        if ($idUnit) {
            $queryPendapatan->where('id_unit', $idUnit);
            $queryPengeluaran->where('id_unit', $idUnit);
            // Tambahkan filter unit ke query DetailPendapatan melalui relasi
            $queryDetailPendapatan->whereHas('pendapatan', function ($query) use ($idUnit) {
                $query->where('id_unit', $idUnit);
            });
        }

        if ($tanggalMulai) {
            $queryPendapatan->whereDate('created_at', '>=', $tanggalMulai);
            $queryPengeluaran->whereDate('created_at', '>=', $tanggalMulai);
            $queryDetailPendapatan->whereHas('pendapatan', function ($query) use ($tanggalMulai) {
                $query->whereDate('created_at', '>=', $tanggalMulai);
            });
        }

        if ($tanggalSelesai) {
            $queryPendapatan->whereDate('created_at', '<=', $tanggalSelesai);
            $queryPengeluaran->whereDate('created_at', '<=', $tanggalSelesai);
            $queryDetailPendapatan->whereHas('pendapatan', function ($query) use ($tanggalSelesai) {
                $query->whereDate('created_at', '<=', $tanggalSelesai);
            });
        }

        // 5. Hitung total Pendapatan (Harga Jual)
        $totalPendapatan = $queryPendapatan->sum('total');

        // 6. Hitung total Harga Beli dari semua barang yang terjual
        $totalHargaBeli = $queryDetailPendapatan
            ->join('barang', 'detail_pendapatan.id_barang', '=', 'barang.id_barang')
            ->sum(DB::raw('detail_pendapatan.jumlah * barang.harga_beli'));
        
        // Pastikan hasilnya tidak null
        $totalHargaBeli = $totalHargaBeli ?? 0;

        // 7. Hitung total Pengeluaran
        $totalPengeluaran = $queryPengeluaran->sum('total');

        // 8. Hitung Laba Kotor (Pendapatan - Harga Beli)
        $labaKotor = $totalPendapatan - $totalHargaBeli;
        
        // 9. Hitung Laba Bersih (Laba Kotor - Pengeluaran)
        $labaBersih = $labaKotor - $totalPengeluaran;
        
        // 10. Dapatkan data untuk ditampilkan di tabel
        $pendapatans = $queryPendapatan->with('unit')->get();
        $pengeluarans = $queryPengeluaran->with('unit')->get();
        
        // 11. Kirim data ke view
        return view('rekap.index', compact(
            'units', 
            'totalPendapatan', 
            'totalPengeluaran', 
            'labaKotor', 
            'labaBersih', 
            'pendapatans', 
            'pengeluarans',
            'tanggalMulai',
            'tanggalSelesai',
            'idUnit'
        ));
    }
}