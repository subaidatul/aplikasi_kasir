@extends('layouts.app')

@section('page_title', 'Daftar Barang')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Daftar Barang(item)</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        {{-- Perbaikan: Tambah awalan 'admin.' pada rute --}}
        <a href="{{ route('admin.barang.create') }}" class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded mb-4 inline-block">
            Tambah Barang(item)
        </a>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif
        
        <h2 class="text-2xl font-semibold text-gray-700 mt-6 mb-4 border-b pb-2">Daftar Barang(item) Cafe</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang(item)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang(item)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($barang->gambar)
                                    <img src="{{ Storage::url($barang->gambar) }}" alt="{{ $barang->nama_barang }}" class="h-12 w-12 object-cover rounded-full">
                                @else
                                    <span class="text-gray-400">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $barang->kode_barang }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $barang->nama_barang }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $barang->kategori_produk }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $barang->unit->nama_unit ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $barang->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($barang->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{-- Perbaikan: Tambah awalan 'admin.' pada rute --}}
                                <a href="{{ route('admin.barang.edit', $barang->id_barang) }}" class="text-indigo-600 hover:text-indigo-900 font-bold mr-2">Edit</a>
                                {{-- Perbaikan: Tambah awalan 'admin.' pada rute --}}
                                <form action="{{ route('admin.barang.destroy', $barang->id_barang) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection