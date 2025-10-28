<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Illuminate\Support\Collection;

class PendapatanExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    protected $pendapatan;

    public function __construct(Collection $pendapatan)
    {
        $this->pendapatan = $pendapatan;
    }

    public function collection()
    {
        $data = $this->pendapatan->map(function ($item) {
            return [
                'ID Transaksi' => $item->no_transaksi ?? 'N/A',
                'Jenis' => 'Pendapatan',
                'Tanggal' => \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i'),
                'Unit' => $item->unit->nama_unit ?? 'N/A',
                'Keterangan' => $item->keterangan ?? 'N/A',
                'Total' => $item->total,
            ];
        });

        // Tambahkan baris total
        $totalPendapatan = $data->sum('Total');
        $data->push([
            'ID Transaksi' => null,
            'Jenis' => null,
            'Tanggal' => null,
            'Unit' => 'Total Pendapatan',
            'Keterangan' => null,
            'Total' => $totalPendapatan
        ]);
        
        return $data;
    }

    public function headings(): array
    {
        return ['ID Transaksi', 'Jenis', 'Tanggal', 'Unit', 'Keterangan', 'Total'];
    }

    public function title(): string
    {
        return 'Pendapatan';
    }
}