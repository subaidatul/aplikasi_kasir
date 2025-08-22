@extends('layouts.app')

@section('page_title', 'Struk Transaksi')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Struk Transaksi</h2>

    {{-- Bagian Filter Struk --}}
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Filter Struk</h3>
        <form action="{{ route('struk.index') }}" method="GET" class="flex items-end space-x-4">
            <div>
                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Tampilkan
                </button>
            </div>
        </form>
    </div>

    {{-- Bagian Daftar Struk --}}
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-700">Daftar Struk</h3>
        </div>

        {{-- Bagian Laporan dan Tombol Export --}}
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Laporan</h2>
            <div class="flex gap-2 mb-4">
                {{-- Tautan untuk Export Excel --}}
                <a href="{{ route('laporan.export.excel', [
                    'tanggal_mulai' => request('tanggal_mulai'),
                    'tanggal_selesai' => request('tanggal_selesai')
                ]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Export Excel
                </a>

                {{-- Tautan untuk Cetak PDF --}}
                <a href="{{ route('laporan.export.pdf', [
                    'tanggal_mulai' => request('tanggal_mulai'),
                    'tanggal_selesai' => request('tanggal_selesai')
                ]) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" target="_blank">
                    Cetak PDF
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID Struk
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Unit Usaha
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Loop data struk di sini --}}
                    {{-- @foreach ($struks as $struk)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $struk->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $struk->tanggal }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Rp {{ number_format($struk->total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $struk->unitUsaha->nama }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('struk.show', $struk->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Lihat</a>
                            <a href="#" class="text-red-600 hover:text-red-900">Hapus</a>
                        </td>
                    </tr>
                    @endforeach --}}
                    {{-- Jika data kosong --}}
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data struk.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection