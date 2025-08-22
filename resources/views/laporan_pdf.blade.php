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
    </style>
</head>
<body>

    <h1>Laporan Transaksi</h1>
    <p>Periode: {{ request('tanggal_mulai') }} s/d {{ request('tanggal_selesai') }}</p>

    <h2>Pendapatan</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pendapatan as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>{{ $item->unit->nama_unit ?? 'N/A' }}</td>
                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Pengeluaran</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pengeluaran as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>{{ $item->unit->nama_unit ?? 'N/A' }}</td>
                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>