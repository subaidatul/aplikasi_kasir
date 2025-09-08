@extends('layouts.app')

@section('page_title', 'Edit Barang')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Barang(item): {{ $barang->nama_barang }}</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Ada masalah dengan input Anda!</strong>
                <span class="block sm:inline">Silakan periksa kembali formulir Anda.</span>
            </div>
        @endif

        {{-- PERBAIKAN: Ganti route('barang.update') menjadi route('admin.barang.update') --}}
        <form action="{{ route('admin.barang.update', $barang->id_barang) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="id_unit" class="block text-sm font-medium text-gray-700">Unit Usaha</label>
                    <select name="id_unit" id="id_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Pilih Unit Usaha</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}" {{ old('id_unit', $barang->id_unit) == $unit->id_unit ? 'selected' : '' }}>
                                {{ $unit->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_unit')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang(item)</label>
                    <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('nama_barang')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="kode_barang" class="block text-sm font-medium text-gray-700">Kode Barang(item)</label>
                    <input type="text" name="kode_barang" id="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('kode_barang')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="kategori_produk" class="block text-sm font-medium text-gray-700">Kategori Produk</label>
                    <input type="text" name="kategori_produk" id="kategori_produk" value="{{ old('kategori_produk', $barang->kategori_produk) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('kategori_produk')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan</label>
                    <input type="text" name="satuan" id="satuan" value="{{ old('satuan', $barang->satuan) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('satuan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="harga_beli" class="block text-sm font-medium text-gray-700">Harga Beli</label>
                    <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli', $barang->harga_beli) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="0" required>
                    @error('harga_beli')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="harga_jual" class="block text-sm font-medium text-gray-700">Harga Jual</label>
                    <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual', $barang->harga_jual) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="0" required>
                    @error('harga_jual')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="aktif" @if (old('status', $barang->status) == 'aktif') selected @endif>Aktif</option>
                        <option value="nonaktif" @if (old('status', $barang->status) == 'nonaktif') selected @endif>Nonaktif</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Gambar Saat Ini</label>
                    <div class="mt-2 mb-4">
                        @if ($barang->gambar)
                            <img src="{{ Storage::url($barang->gambar) }}" alt="Gambar {{ $barang->nama_barang }}" class="h-40 w-auto object-cover rounded-md shadow-sm">
                        @else
                            <p class="text-gray-500">Tidak ada gambar saat ini.</p>
                        @endif
                    </div>
                    <label for="gambar" class="block text-sm font-medium text-gray-700">Unggah Gambar Baru (opsional)</label>
                    <input type="file" name="gambar" id="gambar" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('gambar')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            <div class="mt-6">
                <button type="submit" class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded">Update Barang(item)</button>
            </div>
        </form>
    </div>
@endsection