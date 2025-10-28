<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row td { font-weight: bold; background-color: #e0e0e0; }
    </style>
</head>
<body>

    <h1>Laporan Transaksi</h1>
    <p>Periode: {{ request('tanggal_mulai') }} s/d {{ request('tanggal_selesai') }}</p>

    <h2>Pendapatan</h2>
    <table>
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPendapatan = 0;
            @endphp
            @foreach ($pendapatan as $item)
                <tr>
                    <td>{{ $item->no_transaksi ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}</td>
                    <td>{{ $item->keterangan ?? 'N/A' }}</td>
                    <td>{{ $item->unit->nama_unit ?? 'N/A' }}</td>
                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @php
                    $totalPendapatan += $item->total;
                @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total Pendapatan</td>
                <td>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <h2>Pengeluaran</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPengeluaran = 0;
            @endphp
            @foreach ($pengeluaran as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}</td>
                    <td>{{ $item->keterangan ?? 'N/A' }}</td>
                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @php
                    $totalPengeluaran += $item->total;
                @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">Total Pengeluaran</td>
                <td>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>