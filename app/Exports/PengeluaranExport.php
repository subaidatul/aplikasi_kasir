<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Illuminate\Support\Collection;

class PengeluaranExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    protected $pengeluaran;

    public function __construct(Collection $pengeluaran)
    {
        $this->pengeluaran = $pengeluaran;
    }

    public function collection()
    {
        $data = $this->pengeluaran->map(function ($item) {
            return [
                'ID Transaksi' => null,
                'Jenis' => 'Pengeluaran',
                'Tanggal' => \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i'),
                'Keterangan' => $item->keterangan ?? 'N/A',
                'Total' => $item->total,
            ];
        });

        // Tambahkan baris total
        $totalPengeluaran = $data->sum('Total');
        $data->push([
            'ID Transaksi' => null,
            'Jenis' => null,
            'Tanggal' => null,
            'Keterangan' => 'Total Pengeluaran',
            'Total' => $totalPengeluaran
        ]);
        
        return $data;
    }

    public function headings(): array
    {
        return ['ID Transaksi', 'Jenis', 'Tanggal', 'Keterangan', 'Total'];
    }

    public function title(): string
    {
        return 'Pengeluaran';
    }
}