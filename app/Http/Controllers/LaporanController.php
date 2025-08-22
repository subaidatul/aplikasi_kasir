<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanTransaksiExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pendapatan;
use App\Models\Pengeluaran;

class LaporanController extends Controller
{
    public function exportExcel(Request $request)
    {
        $pendapatan = Pendapatan::with('unit')
                                ->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai])
                                ->get()
                                ->map(function ($item) {
                                    $item->jenis_transaksi = 'Pendapatan';
                                    return $item;
                                });

        $pengeluaran = Pengeluaran::with('unit')
                                  ->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai])
                                  ->get()
                                  ->map(function ($item) {
                                      $item->jenis_transaksi = 'Pengeluaran';
                                      return $item;
                                  });

        $data = $pendapatan->merge($pengeluaran);
        
        return Excel::download(new LaporanTransaksiExport($data), 'laporan_transaksi.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $pendapatan = Pendapatan::with('unit')
                                ->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai])
                                ->get();
        
        $pengeluaran = Pengeluaran::with('unit')
                                  ->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai])
                                  ->get();
        
        $pdf = Pdf::loadView('laporan_pdf', compact('pendapatan', 'pengeluaran'));
        
        return $pdf->download('laporan_transaksi.pdf');
    }
}