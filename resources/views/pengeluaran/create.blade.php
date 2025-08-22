@extends('layouts.app')

@section('page_title', 'Tambah Pengeluaran')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Tambah Pengeluaran Baru</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('pengeluaran.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="id_unit" class="block text-sm font-medium text-gray-700">Unit</label>
                    <select name="id_unit" id="id_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ now()->toDateString() }}">
                </div>
            </div>

            <div class="mt-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-800 mb-2">Item Pengeluaran</h3>
                <div id="items-container">
                    </div>
                <button type="button" id="add-item-btn" class="mt-4 text-sm text-blue-500 hover:text-blue-700">
                    + Tambah Item
                </button>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemsContainer = document.getElementById('items-container');
            const addItemBtn = document.getElementById('add-item-btn');
            let itemIndex = 0;

            function createItemRow() {
                const itemRow = document.createElement('div');
                itemRow.classList.add('flex', 'gap-4', 'mb-4', 'items-end');
                itemRow.innerHTML = `
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
                    e.target.closest('.flex').remove();
                }
            });

            // Tambahkan baris pertama secara default
            createItemRow();
        });
    </script>
@endsection