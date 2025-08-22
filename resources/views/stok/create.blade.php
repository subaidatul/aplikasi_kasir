@extends('layouts.app')

@section('page_title', 'Tambah Stok Barang')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Tambah Stok Barang</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('stok.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="id_barang" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                    <select name="id_barang" id="id_barang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id_barang }}">{{ $barang->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="id_unit" class="block text-sm font-medium text-gray-700">Unit</label>
                    <select name="id_unit" id="id_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="mt-6">
                <label for="jumlah_stok" class="block text-sm font-medium text-gray-700">Jumlah Stok Masuk</label>
                <input type="number" name="jumlah_stok" id="jumlah_stok" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>
            
            <div class="mt-6">
                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ now()->toDateString() }}" required>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Simpan Stok
                </button>
                <a href="{{ route('stok.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection