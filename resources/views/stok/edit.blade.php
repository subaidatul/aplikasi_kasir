@extends('layouts.app')

@section('page_title', 'Edit Stok Barang')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Stok Barang</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- PERBAIKAN: Mengganti route('stok.update') menjadi route('admin.stok.update') --}}
        <form action="{{ route('admin.stok.update', $stok->id_stok) }}" method="POST">
            @csrf
            @method('PUT') {{-- Penting: Gunakan method PUT untuk proses update --}}
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="id_barang" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                    <select name="id_barang" id="id_barang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id_barang }}" {{ $stok->id_barang == $barang->id_barang ? 'selected' : '' }}>
                                {{ $barang->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="id_unit" class="block text-sm font-medium text-gray-700">Unit</label>
                    <select name="id_unit" id="id_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}" {{ $stok->id_unit == $unit->id_unit ? 'selected' : '' }}>
                                {{ $unit->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="mt-6">
                <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah Stok Masuk</label>
                <input type="number" name="jumlah" id="jumlah" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $stok->stok_masuk }}" required>
            </div>
            
            <div class="mt-6">
                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $stok->tanggal }}" required>
            </div>
            
            <div class="mt-6">
                <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ $stok->keterangan }}</textarea>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                    Perbarui Stok
                </button>
                {{-- PERBAIKAN: Mengganti route('stok.index') menjadi route('admin.stok.index') --}}
                <a href="{{ route('admin.stok.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2 transition-colors duration-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection