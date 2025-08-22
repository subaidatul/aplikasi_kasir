@extends('layouts.app')

@section('page_title', 'Daftar Barang')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Daftar Barang</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        {{-- Tombol untuk menambah barang pendapatan saja --}}
        <a href="{{ route('barang.create', ['type' => 'pendapatan']) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
            Tambah Barang
        </a>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Bagian untuk Daftar Barang Pendapatan Cafe --}}
        <h2 class="text-2xl font-semibold text-gray-700 mt-6 mb-4 border-b pb-2">Daftar Barang Cafe</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Awal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Stok</th>
                         <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($barangPendapatanCafe as $index => $barang)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $barang->kode_barang }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $barang->nama_barang }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $barang->kategori_produk }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $barang->unit->nama_unit ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $barang->stok_awal }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $barang->stok }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $barang->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($barang->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('barang.edit', $barang->id_barang) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                                <form action="{{ route('barang.destroy', $barang->id_barang) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection