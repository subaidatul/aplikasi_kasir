<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }
        .container {
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 10px;
        }
        .header, .footer {
            text-align: center;
        }
        .header h1 {
            font-size: 20px;
            margin: 0 0 5px 0;
        }
        .details {
            margin: 15px 0;
        }
        .details p {
            margin: 2px 0;
        }
        .items {
            border-top: 1px dashed #ccc;
            border-bottom: 1px dashed #ccc;
            padding: 10px 0;
            margin-bottom: 15px;
        }
        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .item-name {
            flex: 1;
        }
        .item-qty, .item-price {
            width: 50px;
            text-align: right;
        }
        .item-total {
            width: 70px;
            text-align: right;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }
        .button-print {
            text-align: center;
            margin-top: 20px;
        }
        @media print {
            .button-print {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Kasir Rest Area</h1>
            <p>Bukti Transaksi</p>
            <p>-------------------------</p>
        </div>

        <div class="details">
            @if ($jenis == 'pendapatan')
                <p>No. Transaksi: {{ $data->no_pendapatan }}</p>
                <p>Tanggal: {{ \Carbon\Carbon::parse($data->tanggal)->format('d-m-Y') }}</p>
                <p>Unit Usaha: {{ $data->unit->nama_unit }}</p>
                <p>Deskripsi: {{ $data->deskripsi }}</p>
            @else
                {{-- Perbaikan: Hapus baris 'Unit Usaha' untuk pengeluaran --}}
                <p>No. Transaksi: {{ $data->no_pengeluaran }}</p>
                <p>Tanggal: {{ \Carbon\Carbon::parse($data->tanggal)->format('d-m-Y') }}</p>
                <p>Deskripsi: {{ $data->deskripsi }}</p>
            @endif
        </div>

        <div class="items">
            @if ($jenis == 'pendapatan')
                @foreach ($data->detailPendapatan as $item)
                    <div class="item">
                        <span class="item-name">{{ $item->barang->nama_barang }}</span>
                        <span class="item-qty">{{ $item->jumlah }}x</span>
                        <span class="item-price">{{ number_format($item->barang->harga_jual, 0, ',', '.') }}</span>
                        <span class="item-total">{{ number_format($item->total, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            @else
                @foreach ($data->details as $item)
                    <div class="item">
                        <span class="item-name">{{ $item->nama_keperluan }}</span>
                        <span class="item-qty">{{ $item->jumlah }}x</span>
                        {{-- Hitung harga dari total / jumlah --}}
                        <span class="item-price">{{ number_format($item->total / $item->jumlah, 0, ',', '.') }}</span>
                        <span class="item-total">{{ number_format($item->total, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            @endif
        </div>
        
        <div class="total-row">
            <span>Total</span>
            <span>Rp {{ number_format($data->total, 0, ',', '.') }}</span>
        </div>

        <div class="footer">
            <p>-------------------------</p>
            <p>Terima Kasih</p>
        </div>
    </div>
    
    <div class="button-print">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Struk</button>
    </div>
</body>
</html>