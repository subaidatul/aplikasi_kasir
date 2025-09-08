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
                            <option value="{{ $unit->id_unit }}" {{ old('id_unit') == $unit->id_unit ? 'selected' : '' }}>{{ $unit->nama_unit }}</option>
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
                        @if(old('items'))
                            @foreach(old('items') as $index => $item)
                                <div class="item-row flex items-center gap-4 mb-4">
                                    <select name="items[{{ $index }}][id_barang]" class="flex-1 rounded-md border-gray-300 shadow-sm item-select">
                                        <option value="">Pilih Barang</option>
                                        @foreach($groupedBarangs[1] as $barang)
                                            <option value="{{ $barang->id_barang }}" {{ old("items.{$index}.id_barang") == $barang->id_barang ? 'selected' : '' }}>
                                                {{ $barang->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="items[{{ $index }}][jumlah]" class="w-24 rounded-md border-gray-300 shadow-sm" value="{{ old("items.{$index}.jumlah") }}" min="1">
                                    <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                                </div>
                            @endforeach
                        @else
                            <div class="item-row flex items-center gap-4 mb-4">
                                <select name="items[0][id_barang]" class="flex-1 rounded-md border-gray-300 shadow-sm item-select">
                                    <option value="">Pilih Barang</option>
                                    @if(isset($groupedBarangs[1]))
                                        @foreach($groupedBarangs[1] as $barang)
                                            <option value="{{ $barang->id_barang }}">{{ $barang->nama_barang }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <input type="number" name="items[0][jumlah]" class="w-24 rounded-md border-gray-300 shadow-sm" value="1" min="1">
                                <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addItem()" class="text-blue-500 hover:text-blue-700 font-bold mb-4">+ Tambah Item</button>
                    <div class="mt-6">
                        <label for="deskripsi_cafe" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_cafe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                {{-- Form untuk Sewa Tempat --}}
                <div id="form-sewa-tempat" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_penyewa" class="block text-sm font-medium text-gray-700">Nama Penyewa</label>
                            <input type="text" name="nama_penyewa" id="nama_penyewa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('nama_penyewa') }}" disabled>
                        </div>
                        <div>
                            <label for="harga_awal" class="block text-sm font-medium text-gray-700">Harga Awal</label>
                            <input type="number" name="harga_awal" id="harga_awal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('harga_awal') }}" disabled>
                        </div>
                        <div>
                            <label for="diskon" class="block text-sm font-medium text-gray-700">Diskon (%)</label>
                            <input type="number" name="diskon" id="diskon" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('diskon', 0) }}" disabled>
                        </div>
                        <div>
                            <label for="harga_akhir" class="block text-sm font-medium text-gray-700">Harga Akhir</label>
                            <input type="number" name="harga_akhir" id="harga_akhir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('harga_akhir') }}" readonly disabled>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="deskripsi_sewa" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_sewa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled>{{ old('deskripsi') }}</textarea>
                    </div>
                </div>
                
                {{-- Form untuk Seluncuran --}}
                <div id="form-seluncuran" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tiket_terjual" class="block text-sm font-medium text-gray-700">Tiket Terjual</label>
                            <input type="number" name="tiket_terjual" id="tiket_terjual" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('tiket_terjual') }}" disabled>
                        </div>
                        <div>
                            <label for="harga_tiket" class="block text-sm font-medium text-gray-700">Harga Tiket</label>
                            <input type="number" name="harga_tiket" id="harga_tiket" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('harga_tiket') }}" disabled>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="deskripsi_seluncuran" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_seluncuran" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled>{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                {{-- Form untuk ATV --}}
                <div id="form-atv" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="jumlah_sewa" class="block text-sm font-medium text-gray-700">Jumlah Sewa</label>
                            <input type="number" name="jumlah_sewa" id="jumlah_sewa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('jumlah_sewa') }}" disabled>
                        </div>
                        <div>
                            <label for="durasi_sewa" class="block text-sm font-medium text-gray-700">Durasi (Jam)</label>
                            <input type="number" name="durasi_sewa" id="durasi_sewa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('durasi_sewa') }}" disabled>
                        </div>
                        <div>
                            <label for="tarif_sewa" class="block text-sm font-medium text-gray-700">Tarif (Harga)</label>
                            <input type="number" name="tarif_sewa" id="tarif_sewa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('tarif_sewa') }}" disabled>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="deskripsi_atv" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_atv" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled>{{ old('deskripsi') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded">Simpan Transaksi</button>
            </div>
        </form>
    </div>
@endsection

{{-- Simpan data barang di dalam skrip agar bisa diakses JS --}}
<script>
    const groupedBarangs = @json($groupedBarangs);
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const unitSelect = document.getElementById('unit_id');
        const dynamicFormArea = document.getElementById('dynamic-form-area');
        const forms = {
            '1': document.getElementById('form-cafe-etc'),
            '2': document.getElementById('form-sewa-tempat'),
            '3': document.getElementById('form-seluncuran'),
            '4': document.getElementById('form-atv'),
        };

        function showForm(unitId) {
            Object.values(forms).forEach(form => {
                form.classList.add('hidden');
                form.querySelectorAll('input, select, textarea').forEach(input => {
                    input.setAttribute('disabled', 'disabled');
                });
            });

            if (forms[unitId]) {
                forms[unitId].classList.remove('hidden');
                forms[unitId].querySelectorAll('input, select, textarea').forEach(input => {
                    input.removeAttribute('disabled');
                });
            }
        }

        function updateItemOptions(unitId, selectElement = null, selectedValue = null) {
            const barangList = groupedBarangs[unitId] || [];
            
            const renderOptions = (select) => {
                select.innerHTML = '<option value="">Pilih Barang</option>';
                barangList.forEach(barang => {
                    const option = document.createElement('option');
                    option.value = barang.id_barang;
                    option.textContent = barang.nama_barang;
                    if (selectedValue && selectedValue == barang.id_barang) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            };

            if (selectElement) {
                renderOptions(selectElement);
            } else {
                const itemSelects = document.querySelectorAll('.item-select');
                itemSelects.forEach(renderOptions);
            }
        }
        
        function addItem() {
            const itemList = document.getElementById('item-list');
            const newIndex = itemList.querySelectorAll('.item-row').length;

            const newItemRow = document.createElement('div');
            newItemRow.classList.add('item-row', 'flex', 'items-center', 'gap-4', 'mb-4');
            newItemRow.innerHTML = `
                <select name="items[${newIndex}][id_barang]" class="flex-1 rounded-md border-gray-300 shadow-sm item-select" required>
                    <option value="">Pilih Barang</option>
                </select>
                <input type="number" name="items[${newIndex}][jumlah]" class="w-24 rounded-md border-gray-300 shadow-sm" value="1" min="1" required>
                <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
            `;
            
            itemList.appendChild(newItemRow);
            const newItemSelect = newItemRow.querySelector('select');
            updateItemOptions(unitSelect.value, newItemSelect);
        }

        window.removeItem = function(button) {
            button.closest('.item-row').remove();
            // Re-index remaining items to avoid gaps
            document.querySelectorAll('#item-list .item-row').forEach((row, index) => {
                row.querySelector('select').name = `items[${index}][id_barang]`;
                row.querySelector('input').name = `items[${index}][jumlah]`;
            });
        }
        
        // Initial setup on page load
        const oldUnitId = '{{ old('id_unit') }}';
        if (oldUnitId) {
            showForm(oldUnitId);
            updateItemOptions(oldUnitId);
            // Re-populate non-item fields
            document.getElementById('deskripsi_sewa').value = '{{ old('deskripsi') }}';
            document.getElementById('deskripsi_seluncuran').value = '{{ old('deskripsi') }}';
            document.getElementById('deskripsi_atv').value = '{{ old('deskripsi') }}';
            document.getElementById('nama_penyewa').value = '{{ old('nama_penyewa') }}';
            document.getElementById('harga_awal').value = '{{ old('harga_awal') }}';
            document.getElementById('diskon').value = '{{ old('diskon') }}';
            document.getElementById('harga_akhir').value = '{{ old('harga_akhir') }}';
            document.getElementById('tiket_terjual').value = '{{ old('tiket_terjual') }}';
            document.getElementById('harga_tiket').value = '{{ old('harga_tiket') }}';
            document.getElementById('jumlah_sewa').value = '{{ old('jumlah_sewa') }}';
            document.getElementById('durasi_sewa').value = '{{ old('durasi_sewa') }}';
            document.getElementById('tarif_sewa').value = '{{ old('tarif_sewa') }}';
        } else {
            showForm(unitSelect.value);
            updateItemOptions(unitSelect.value);
        }
        
        unitSelect.addEventListener('change', function () {
            showForm(this.value);
            updateItemOptions(this.value);
        });

        window.addItem = addItem;
        
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