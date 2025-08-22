@extends('layouts.app')

@section('page_title', 'Tambah Pendapatan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Tambah Pendapatan Baru</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">Ada masalah dengan input Anda.</span>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif
        
        <form id="form-pendapatan" action="{{ route('pendapatan.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700">Unit Usaha</label>
                    <select name="id_unit" id="unit_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Pilih Unit Usaha</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
            </div>

            <div id="dynamic-form-area">
                {{-- Form untuk Cafe, Wifi, Parkir --}}
                <div id="form-cafe-etc" class="hidden">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Item Pendapatan</h3>
                    <div id="item-list">
                        <div class="item-row flex items-center gap-4 mb-4">
                            <select name="items[0][id_barang]" class="flex-1 rounded-md border-gray-300 shadow-sm item-select" disabled>
                                <option value="">Pilih Barang(item)</option>
                            </select>
                            <input type="number" name="items[0][jumlah]" class="w-24 rounded-md border-gray-300 shadow-sm" value="1" min="1" disabled>
                            <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700 font-bold" disabled>Hapus</button>
                        </div>
                    </div>
                    <button type="button" onclick="addItem()" class="text-blue-500 hover:text-blue-700 font-bold mb-4">+ Tambah Item</button>
                    <div class="mt-6">
                        <label for="deskripsi_cafe" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_cafe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled></textarea>
                    </div>
                </div>

                {{-- Form untuk Sewa Tempat --}}
                <div id="form-sewa-tempat" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_penyewa" class="block text-sm font-medium text-gray-700">Nama Penyewa</label>
                            <input type="text" name="nama_penyewa" id="nama_penyewa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled>
                        </div>
                        <div>
                            <label for="harga_awal" class="block text-sm font-medium text-gray-700">Harga Awal</label>
                            <input type="number" name="harga_awal" id="harga_awal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled>
                        </div>
                        <div>
                            <label for="diskon" class="block text-sm font-medium text-gray-700">Diskon (%)</label>
                            <input type="number" name="diskon" id="diskon" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="0" disabled>
                        </div>
                        <div>
                            <label for="harga_akhir" class="block text-sm font-medium text-gray-700">Harga Akhir</label>
                            <input type="number" name="harga_akhir" id="harga_akhir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly disabled>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="deskripsi_sewa" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_sewa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled></textarea>
                    </div>
                </div>
                
                {{-- Form untuk Seluncuran --}}
                <div id="form-seluncuran" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tiket_terjual" class="block text-sm font-medium text-gray-700">Tiket Terjual</label>
                            <input type="number" name="tiket_terjual" id="tiket_terjual" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled>
                        </div>
                        <div>
                            <label for="harga_tiket" class="block text-sm font-medium text-gray-700">Harga Tiket</label>
                            <input type="number" name="harga_tiket" id="harga_tiket" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="deskripsi_seluncuran" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_seluncuran" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled></textarea>
                    </div>
                </div>

                {{-- Form untuk ATV --}}
                <div id="form-atv" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="jumlah_sewa" class="block text-sm font-medium text-gray-700">Jumlah Sewa</label>
                            <input type="number" name="jumlah_sewa" id="jumlah_sewa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled>
                        </div>
                        <div>
                            <label for="durasi_tarif" class="block text-sm font-medium text-gray-700">Durasi & Tarif</label>
                            <input type="text" name="durasi_tarif" id="durasi_tarif" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="deskripsi_atv" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_atv" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Simpan Transaksi</button>
            </div>
        </form>
    </div>

    {{-- Simpan data barang di dalam skrip agar bisa diakses JS --}}
    <script>
        const groupedBarangs = @json($groupedBarangs);
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const unitSelect = document.getElementById('unit_id');
            const forms = {
                '1': document.getElementById('form-cafe-etc'),
                '2': document.getElementById('form-sewa-tempat'),
                '3': document.getElementById('form-seluncuran'),
                '4': document.getElementById('form-atv'),
            };

            function showForm(unitId) {
                Object.values(forms).forEach(form => {
                    form.classList.add('hidden');
                    form.querySelectorAll('input, select, textarea, button').forEach(input => {
                        input.setAttribute('disabled', 'disabled');
                    });
                });

                if (forms[unitId]) {
                    forms[unitId].classList.remove('hidden');
                    forms[unitId].querySelectorAll('input, select, textarea, button').forEach(input => {
                        input.removeAttribute('disabled');
                    });
                }
            }

            function updateItemOptions(unitId) {
                const itemSelects = document.querySelectorAll('.item-select');
                itemSelects.forEach(select => {
                    select.innerHTML = '<option value="">Pilih Barang</option>';
                    if (groupedBarangs[unitId]) {
                        groupedBarangs[unitId].forEach(barang => {
                            const option = document.createElement('option');
                            option.value = barang.id_barang;
                            option.textContent = barang.nama_barang;
                            select.appendChild(option);
                        });
                    }
                });
            }

            showForm(unitSelect.value);
            updateItemOptions(unitSelect.value);

            unitSelect.addEventListener('change', function () {
                showForm(this.value);
                updateItemOptions(this.value);
            });

            let itemCounter = 0;
            window.addItem = function() {
                const itemList = document.getElementById('item-list');
                const newItemRow = document.createElement('div');
                newItemRow.classList.add('item-row', 'flex', 'items-center', 'gap-4', 'mb-4');
                itemCounter++;
                newItemRow.innerHTML = `
                    <select name="items[${itemCounter}][id_barang]" class="flex-1 rounded-md border-gray-300 shadow-sm item-select" required>
                        <option value="">Pilih Barang</option>
                    </select>
                    <input type="number" name="items[${itemCounter}][jumlah]" class="w-24 rounded-md border-gray-300 shadow-sm" value="1" min="1" required>
                    <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                `;
                itemList.appendChild(newItemRow);
                updateItemOptions(unitSelect.value);
            }

            window.removeItem = function(button) {
                button.closest('.item-row').remove();
            }

            const hargaAwalInput = document.getElementById('harga_awal');
            const diskonInput = document.getElementById('diskon');
            const hargaAkhirInput = document.getElementById('harga_akhir');

            function calculateHargaAkhir() {
                const hargaAwal = parseFloat(hargaAwalInput.value) || 0;
                const diskon = parseFloat(diskonInput.value) || 0;
                if (hargaAwal > 0) {
                    const hargaDiskon = hargaAwal * (diskon / 100);
                    const hargaAkhir = hargaAwal - hargaDiskon;
                    hargaAkhirInput.value = hargaAkhir.toFixed(0);
                } else {
                    hargaAkhirInput.value = '';
                }
            }

            hargaAwalInput.addEventListener('input', calculateHargaAkhir);
            diskonInput.addEventListener('input', calculateHargaAkhir);
        });
    </script>
@endsection