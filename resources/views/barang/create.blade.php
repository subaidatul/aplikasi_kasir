@extends('layouts.app')

@section('page_title', 'Tambah Barang')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Tambah Barang Baru</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('barang.store') }}" method="POST">
            @csrf
            
            {{-- Menggunakan nilai dari konfigurasi untuk id_unit --}}
            <input type="hidden" name="id_unit" value="{{ config('app.cafe_unit_id', 1) }}">

            {{-- Menampilkan pesan error validasi secara umum --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Ada masalah dengan input Anda!</strong>
                    <span class="block sm:inline">Silakan periksa kembali formulir Anda.</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required value="{{ old('nama_barang') }}">
                    @error('nama_barang')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                 <div>
                    <label for="kode_barang" class="block text-sm font-medium text-gray-700">Kode Barang</label>
                    <input type="text" name="kode_barang" id="kode_barang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required value="{{ old('kode_barang') }}">
                    @error('kode_barang')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="kategori_produk" class="block text-sm font-medium text-gray-700">Kategori Produk</label>
                    <input type="text" name="kategori_produk" id="kategori_produk" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required value="{{ old('kategori_produk') }}">
                    @error('kategori_produk')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Input terpisah untuk Stok Awal dan Sisa Stok --}}
                <div>
                    <label for="stok_awal" class="block text-sm font-medium text-gray-700">Stok Awal</label>
                    <input type="number" name="stok_awal" id="stok_awal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="0" required value="{{ old('stok_awal') }}">
                    @error('stok_awal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="stok" class="block text-sm font-medium text-gray-700">Sisa Stok</label>
                    <input type="number" name="stok" id="stok" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="0" required value="{{ old('stok') }}">
                    @error('stok')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Akhir Input terpisah --}}

                <div>
                    <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan (Unit)</label>
                    <input type="text" name="satuan" id="satuan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required value="{{ old('satuan') }}">
                    @error('satuan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="harga_beli" class="block text-sm font-medium text-gray-700">Harga Beli</label>
                    <input type="number" name="harga_beli" id="harga_beli" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="0" required value="{{ old('harga_beli') }}">
                    @error('harga_beli')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="harga_jual" class="block text-sm font-medium text-gray-700">Harga Jual</label>
                    <input type="number" name="harga_jual" id="harga_jual" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="0" required value="{{ old('harga_jual') }}">
                    @error('harga_jual')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Simpan Barang
                </button>
            </div>
        </form>
    </div>
@endsection