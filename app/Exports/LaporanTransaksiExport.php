<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon; 

class LaporanTransaksiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Jenis',
            'Tanggal',
            'Unit',
            'Keterangan',
            'Total'
        ];
    }

    public function map($row): array
    {
        $jenis = $row->jenis_transaksi ?? 'N/A';
        
        // Perbaikan: Ubah string tanggal menjadi objek Carbon sebelum diformat
        $tanggal = $row->tanggal ?? $row->created_at;
        $tanggal_formatted = $tanggal ? Carbon::parse($tanggal)->format('d-m-Y H:i') : 'N/A';

        return [
            $row->id ?? 'N/A',
            $jenis,
            $tanggal_formatted,
            $row->unit->nama_unit ?? 'N/A',
            $row->keterangan ?? 'N/A',
            $row->total
        ];
    }
}