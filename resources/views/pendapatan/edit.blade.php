@extends('layouts.app')

@section('page_title', 'Edit Pendapatan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Pendapatan</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Menampilkan pesan error validasi secara umum --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Ada masalah dengan input Anda!</strong>
                <span class="block sm:inline">Silakan periksa kembali formulir Anda.</span>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('pendapatan.update', $pendapatan->id_pendapatan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="id_unit" class="block text-gray-700 text-sm font-bold mb-2">Unit</label>
                <input type="text" value="{{ $pendapatan->unit->nama_unit ?? 'N/A' }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 cursor-not-allowed leading-tight focus:outline-none focus:shadow-outline" disabled>
                <input type="hidden" name="id_unit" id="id_unit" value="{{ $pendapatan->id_unit }}">
            </div>

            {{-- Logika Blade untuk menampilkan form berdasarkan Unit ID --}}
            @switch($pendapatan->id_unit)
                @case(1) {{-- Cafe/Wifi/Parkir (Barang) --}}
                    <div id="form-cafe-etc">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Item Pendapatan</h3>
                        <div id="item-list">
                            {{-- Looping untuk menampilkan item yang sudah ada --}}
                            @foreach ($pendapatan->detailPendapatan as $index => $detail)
                                <div class="item-row flex items-center gap-4 mb-4">
                                    <select name="items[{{ $index }}][id_barang]" class="flex-1 rounded-md border-gray-300 shadow-sm item-select" required>
                                        <option value="">Pilih Barang</option>
                                        @foreach ($groupedBarangs[1] as $barang)
                                            <option value="{{ $barang->id_barang }}" {{ $detail->id_barang == $barang->id_barang ? 'selected' : '' }}>
                                                {{ $barang->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="items[{{ $index }}][jumlah]" class="w-24 rounded-md border-gray-300 shadow-sm" value="{{ old('items.' . $index . '.jumlah', $detail->jumlah) }}" min="1" required>
                                    <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" onclick="addItem()" class="text-blue-500 hover:text-blue-700 font-bold mb-4">+ Tambah Item</button>
                        <div class="mt-6">
                            <label for="deskripsi_cafe" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" id="deskripsi_cafe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('deskripsi', $pendapatan->deskripsi) }}</textarea>
                        </div>
                    </div>
                    @break
                @case(2) {{-- Sewa Tempat --}}
                    <div id="form-sewa-tempat">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama_penyewa" class="block text-sm font-medium text-gray-700">Nama Penyewa</label>
                                <input type="text" name="nama_penyewa" id="nama_penyewa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('nama_penyewa', $pendapatan->nama_penyewa) }}" required>
                            </div>
                            <div>
                                <label for="harga_akhir" class="block text-sm font-medium text-gray-700">Harga Akhir</label>
                                <input type="number" name="harga_akhir" id="harga_akhir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('harga_akhir', $pendapatan->total) }}" min="0" required>
                            </div>
                        </div>
                    </div>
                    @break
                @case(3) {{-- Seluncuran --}}
                    <div id="form-seluncuran">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tiket_terjual" class="block text-sm font-medium text-gray-700">Tiket Terjual</label>
                                <input type="number" name="tiket_terjual" id="tiket_terjual" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('tiket_terjual', optional($pendapatan)->tiket_terjual) }}" required>
                            </div>
                            <div>
                                <label for="harga_tiket" class="block text-sm font-medium text-gray-700">Harga Tiket</label>
                                <input type="number" name="harga_tiket" id="harga_tiket" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('harga_tiket', optional($pendapatan)->harga_tiket) }}" required>
                            </div>
                        </div>
                    </div>
                    @break
                @case(4) {{-- ATV --}}
                    <div id="form-atv">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="jumlah_sewa" class="block text-sm font-medium text-gray-700">Jumlah Sewa</label>
                                <input type="number" name="jumlah_sewa" id="jumlah_sewa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('jumlah_sewa', optional($pendapatan)->jumlah_sewa) }}" required>
                            </div>
                        </div>
                    </div>
                    @break
            @endswitch

            <div class="mb-4">
                <label for="tanggal" class="block text-gray-700 text-sm font-bold mb-2">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', \Carbon\Carbon::parse($pendapatan->tanggal)->format('Y-m-d')) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('deskripsi', $pendapatan->deskripsi) }}</textarea>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Pendapatan
                </button>
                <a href="{{ route('pendapatan.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Script untuk fungsi dinamis seperti tambah/hapus item --}}
    @switch($pendapatan->id_unit)
        @case(1)
            <script>
                const groupedBarangs = @json($groupedBarangs);
                let itemCounter = {{ $pendapatan->detailPendapatan->count() }};
                
                function updateItemOptions(selectElement) {
                    const unitId = document.getElementById('id_unit').value;
                    const selectedItemId = selectElement.value;
                    
                    selectElement.innerHTML = '<option value="">Pilih Barang</option>';
                    
                    if (groupedBarangs[unitId]) {
                        groupedBarangs[unitId].forEach(barang => {
                            const option = document.createElement('option');
                            option.value = barang.id_barang;
                            option.textContent = barang.nama_barang;
                            if (barang.id_barang == selectedItemId) {
                                option.selected = true;
                            }
                            selectElement.appendChild(option);
                        });
                    }
                }

                function addItem() {
                    const itemList = document.getElementById('item-list');
                    const newItemRow = document.createElement('div');
                    newItemRow.classList.add('item-row', 'flex', 'items-center', 'gap-4', 'mb-4');
                    newItemRow.innerHTML = `
                        <select name="items[${itemCounter}][id_barang]" class="flex-1 rounded-md border-gray-300 shadow-sm item-select" required>
                            <option value="">Pilih Barang</option>
                        </select>
                        <input type="number" name="items[${itemCounter}][jumlah]" class="w-24 rounded-md border-gray-300 shadow-sm" value="1" min="1" required>
                        <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                    `;
                    itemList.appendChild(newItemRow);
                    updateItemOptions(newItemRow.querySelector('.item-select'));
                    itemCounter++;
                }

                function removeItem(button) {
                    button.closest('.item-row').remove();
                }

                // Inisialisasi dropdown untuk item yang sudah ada
                document.querySelectorAll('.item-select').forEach(select => {
                    updateItemOptions(select);
                });
            </script>
            @break
        {{-- Jika ada unit lain yang membutuhkan skrip JavaScript, tambahkan di sini --}}
    @endswitch
@endsection