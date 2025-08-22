@extends('layouts.app')

@section('page_title', 'Daftar Stok')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Daftar Stok</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Ganti tombol "Tambah Barang" dengan "Tambah Stok" --}}
        <a href="{{ route('stok.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
            Tambah Stok
        </a>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UNIT USAHA</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA BARANG</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SATUAN</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">STOK MASUK</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">STOK KELUAR</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">SISA</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HARGA (JUAL)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TOTAL (NILAI STOK)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($stokData as $index => $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item['unit'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item['nama_barang'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item['satuan'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">{{ $item['stok_masuk'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">{{ $item['stok_keluar'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">{{ $item['sisa'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('barang.edit', $item['id_barang']) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection