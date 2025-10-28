<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class LaporanTransaksiSheetsExport implements WithMultipleSheets
{
    use Exportable;

    protected $pendapatan;
    protected $pengeluaran;

    public function __construct(Collection $pendapatan, Collection $pengeluaran)
    {
        $this->pendapatan = $pendapatan;
        $this->pengeluaran = $pengeluaran;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: Pendapatan
        $sheets[] = new PendapatanExport($this->pendapatan);

        // Sheet 2: Pengeluaran
        $sheets[] = new PengeluaranExport($this->pengeluaran);

        return $sheets;
    }
}