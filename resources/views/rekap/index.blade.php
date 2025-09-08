@extends('layouts.app')

@section('page_title', 'Rekap Transaksi')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Rekap Transaksi</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Filter Rekap</h2>
        {{-- Perbaikan: Ganti rute 'rekap.index' menjadi 'admin.rekap.index' --}}
        <form action="{{ route('admin.rekap.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="id_unit" class="block text-sm font-medium text-gray-700">Unit Usaha</label>
                    <select name="id_unit" id="id_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Unit</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}"
                                {{ request('id_unit') == $unit->id_unit ? 'selected' : '' }}>
                                {{ $unit->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                        value="{{ request('tanggal_selesai') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded w-full">
                        Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <h2 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Total Pendapatan</h2>
            <p class="text-3xl font-bold text-gray-800 mt-2">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <h2 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Total Pengeluaran</h2>
            <p class="text-3xl font-bold text-gray-800 mt-2">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <h2 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Laba Bersih</h2>
            <p class="text-3xl font-bold text-gray-800 mt-2">Rp {{ number_format($labaBersih, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Laporan</h2>
        <div class="flex gap-2 mb-4">
            {{-- Perbaikan: Ganti rute 'laporan.export.excel' menjadi 'admin.laporan.export.excel' --}}
            <a href="{{ route('admin.laporan.export.excel', [
                'tanggal_mulai' => request('tanggal_mulai'),
                'tanggal_selesai' => request('tanggal_selesai'),
                'id_unit' => request('id_unit')
            ]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Export Excel
            </a>

            {{-- Perbaikan: Ganti rute 'laporan.export.pdf' menjadi 'admin.laporan.export.pdf' --}}
            <a href="{{ route('admin.laporan.export.pdf', [
                'tanggal_mulai' => request('tanggal_mulai'),
                'tanggal_selesai' => request('tanggal_selesai'),
                'id_unit' => request('id_unit')
            ]) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" target="_blank">
                Cetak PDF
            </a>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mt-6 mb-2">Rincian Pendapatan</h3>
        <div class="overflow-x-auto mb-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TANGGAL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UNIT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO TRANSAKSI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TOTAL</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pendapatans as $index => $pendapatan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($pendapatan->tanggal)->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pendapatan->unit->nama_unit ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pendapatan->no_pendapatan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($pendapatan->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data pendapatan yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mt-6 mb-2">Rincian Pengeluaran</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TANGGAL</th>
                        {{-- Perbaikan: Hapus kolom UNIT --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO TRANSAKSI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TOTAL</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pengeluarans as $index => $pengeluaran)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d-m-Y') }}</td>
                            {{-- Perbaikan: Hapus kolom data UNIT --}}
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pengeluaran->no_pengeluaran }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($pengeluaran->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data pengeluaran yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection