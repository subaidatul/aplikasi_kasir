@extends('layouts.app')

@section('page_title', 'Edit Pengeluaran')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Pengeluaran</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- PERBAIKAN: Ganti rute 'pengeluaran.update' menjadi 'admin.pengeluaran.update' --}}
        <form action="{{ route('admin.pengeluaran.update', $pengeluaran->id_pengeluaran) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- PERBAIKAN: Input hidden untuk id_user agar bisa diproses di controller --}}
                <input type="hidden" name="id_user" value="{{ Auth::user()->id_user }}">

                <div>
                    <label for="id_unit" class="block text-sm font-medium text-gray-700">Unit</label>
                    <select name="id_unit" id="id_unit"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}"
                                {{ $pengeluaran->id_unit == $unit->id_unit ? 'selected' : '' }}>
                                {{ $unit->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        value="{{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('Y-m-d') }}">
                </div>
            </div>

            <div class="mt-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ $pengeluaran->deskripsi }}</textarea>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-800 mb-2">Item Pengeluaran</h3>
                <div id="items-container">
                    @foreach ($pengeluaran->details as $index => $detail)
                        <div class="flex gap-4 mb-4 items-end item-row">
                            {{-- PERBAIKAN: Ganti nama input hidden menjadi 'id_detail_pengeluaran' --}}
                            <input type="hidden" name="items[{{ $index }}][id_detail_pengeluaran]" value="{{ $detail->id_detail_pengeluaran }}">
                            <div class="flex-1">
                                <label for="item_keperluan_{{ $index }}"
                                    class="block text-sm font-medium text-gray-700">Keperluan</label>
                                <input type="text" name="items[{{ $index }}][nama_keperluan]"
                                    id="item_keperluan_{{ $index }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ $detail->nama_keperluan }}" required>
                            </div>
                            <div class="w-24">
                                <label for="item_jumlah_{{ $index }}"
                                    class="block text-sm font-medium text-gray-700">Jumlah</label>
                                <input type="number" name="items[{{ $index }}][jumlah]"
                                    id="item_jumlah_{{ $index }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="1"
                                    value="{{ $detail->jumlah }}" required>
                            </div>
                            <div class="w-28">
                                <label for="item_harga_{{ $index }}"
                                    class="block text-sm font-medium text-gray-700">Harga</label>
                                <input type="number" name="items[{{ $index }}][harga]"
                                    id="item_harga_{{ $index }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="0"
                                    {{-- PERBAIKAN: Cek apakah jumlah > 0 sebelum menghitung harga --}}
                                    value="{{ $detail->jumlah > 0 ? $detail->total / $detail->jumlah : 0 }}" required>
                            </div>
                            <div>
                                <button type="button"
                                    class="remove-item-btn text-red-500 hover:text-red-700 font-bold">Hapus</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-item-btn" class="mt-4 text-sm text-blue-500 hover:text-blue-700">
                    + Tambah Item
                </button>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">

                    Perbarui Transaksi
                </button>
                {{-- PERBAIKAN: Ganti rute 'pengeluaran.index' menjadi 'admin.pengeluaran.index' --}}
                <a href="{{ route('admin.pengeluaran.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemsContainer = document.getElementById('items-container');
            const addItemBtn = document.getElementById('add-item-btn');

            // PERBAIKAN: Hitung index baru setelah item yang sudah ada
            let itemIndex = itemsContainer.children.length;

            function createItemRow() {
                const itemRow = document.createElement('div');
                itemRow.classList.add('flex', 'gap-4', 'mb-4', 'items-end', 'item-row');
                itemRow.innerHTML = `
                    {{-- PERBAIKAN: Ganti id_detail menjadi id_detail_pengeluaran --}}
                    <input type="hidden" name="items[${itemIndex}][id_detail_pengeluaran]" value="">
                    <div class="flex-1">
                        <label for="item_keperluan_${itemIndex}" class="block text-sm font-medium text-gray-700">Keperluan</label>
                        <input type="text" name="items[${itemIndex}][nama_keperluan]" id="item_keperluan_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div class="w-24">
                        <label for="item_jumlah_${itemIndex}" class="block text-sm font-medium text-gray-700">Jumlah</label>
                        <input type="number" name="items[${itemIndex}][jumlah]" id="item_jumlah_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="1" value="1" required>
                    </div>
                    <div class="w-28">
                        <label for="item_harga_${itemIndex}" class="block text-sm font-medium text-gray-700">Harga</label>
                        <input type="number" name="items[${itemIndex}][harga]" id="item_harga_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="0" required>
                    </div>
                    <div>
                        <button type="button" class="remove-item-btn text-red-500 hover:text-red-700 font-bold">Hapus</button>
                    </div>
                `;
                itemsContainer.appendChild(itemRow);
                itemIndex++;
            }

            addItemBtn.addEventListener('click', createItemRow);

            itemsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item-btn')) {
                    e.target.closest('.item-row').remove();
                }
            });
        });
    </script>
@endsection