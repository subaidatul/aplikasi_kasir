<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanTransaksiExport;
use App\Exports\LaporanTransaksiSheetsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pendapatan;
use App\Models\Pengeluaran;

class LaporanController extends Controller
{
    public function exportExcel(Request $request)
{
    // Ambil parameter filter
    $idUnit = $request->input('id_unit');
    $tanggalMulai = $request->input('tanggal_mulai');
    $tanggalSelesai = $request->input('tanggal_selesai');

    // Bangun query untuk pendapatan dengan filter
    $queryPendapatan = Pendapatan::with('unit');
    if ($idUnit) {
        $queryPendapatan->where('id_unit', $idUnit);
    }
    if ($tanggalMulai) {
        $queryPendapatan->whereDate('created_at', '>=', $tanggalMulai);
    }
    if ($tanggalSelesai) {
        $queryPendapatan->whereDate('created_at', '<=', $tanggalSelesai);
    }
    $pendapatan = $queryPendapatan->get();

    // Bangun query untuk pengeluaran dengan filter
    $queryPengeluaran = Pengeluaran::query();
    if ($tanggalMulai) {
        $queryPengeluaran->whereDate('created_at', '>=', $tanggalMulai);
    }
    if ($tanggalSelesai) {
        $queryPengeluaran->whereDate('created_at', '<=', $tanggalSelesai);
    }
    $pengeluaran = $queryPengeluaran->get();

    // Mengirim data pendapatan dan pengeluaran ke kelas ekspor terpisah
    return Excel::download(new LaporanTransaksiSheetsExport($pendapatan, $pengeluaran), 'laporan_transaksi.xlsx');
}

    public function exportPdf(Request $request)
    {
        // 1. Ambil parameter filter dari request
        $idUnit = $request->input('id_unit');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        // 2. Bangun query untuk pendapatan dengan filter
        $queryPendapatan = Pendapatan::with('unit');
        if ($idUnit) {
            $queryPendapatan->where('id_unit', $idUnit);
        }
        if ($tanggalMulai) {
            $queryPendapatan->whereDate('created_at', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $queryPendapatan->whereDate('created_at', '<=', $tanggalSelesai);
        }
        $pendapatan = $queryPendapatan->get();

        // 3. Bangun query untuk pengeluaran dengan filter
        $queryPengeluaran = Pengeluaran::query();
        if ($tanggalMulai) {
            $queryPengeluaran->whereDate('created_at', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $queryPengeluaran->whereDate('created_at', '<=', $tanggalSelesai);
        }
        $pengeluaran = $queryPengeluaran->get();

        // 4. Kirim data ke view PDF dan unduh
        $pdf = Pdf::loadView('laporan_pdf', compact('pendapatan', 'pengeluaran'));
        
        return $pdf->download('laporan_transaksi.pdf');
    }
}