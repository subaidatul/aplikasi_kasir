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
                    <select name="id_unit" id="unit_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        required>
                        <option value="">Pilih Unit Usaha</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}" {{ old('id_unit') == $unit->id_unit ? 'selected' : '' }}>
                                {{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
            </div>

            <div id="dynamic-form-area">
                {{-- Form untuk Cafe & Wifi --}}
                <div id="form-cafe" class="hidden">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Item Pendapatan</h3>
                    <div id="item-list">
                        @if (old('items'))
                            @foreach (old('items') as $index => $item)
                                <div class="item-row flex items-center gap-4 mb-4">
                                    <select name="items[{{ $index }}][id_barang]"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm item-select">
                                        <option value="">Pilih Barang</option>
                                        @foreach ($groupedBarangs[1] as $barang)
                                            <option value="{{ $barang->id_barang }}"
                                                {{ old("items.{$index}.id_barang") == $barang->id_barang ? 'selected' : '' }}>
                                                {{ $barang->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="items[{{ $index }}][jumlah]"
                                        class="w-24 rounded-md border-gray-300 shadow-sm"
                                        value="{{ old("items.{$index}.jumlah") }}" min="1">
                                    <button type="button" onclick="removeItem(this)"
                                        class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                                </div>
                            @endforeach
                        @else
                            <div class="item-row flex items-center gap-4 mb-4">
                                <select name="items[0][id_barang]"
                                    class="flex-1 rounded-md border-gray-300 shadow-sm item-select">
                                    <option value="">Pilih Barang</option>
                                    @if (isset($groupedBarangs[1]))
                                        @foreach ($groupedBarangs[1] as $barang)
                                            <option value="{{ $barang->id_barang }}">{{ $barang->nama_barang }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <input type="number" name="items[0][jumlah]"
                                    class="w-24 rounded-md border-gray-300 shadow-sm" value="1" min="1">
                                <button type="button" onclick="removeItem(this)"
                                    class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addItem()" class="text-blue-500 hover:text-blue-700 font-bold mb-4">+
                        Tambah Item</button>
                    <div class="mt-6">
                        <label for="deskripsi_cafe" class="block text-sm font-medium text-gray-700">Deskripsi
                            (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_cafe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                {{-- Form untuk Kemah Rimbun Rest Area --}}
                <div id="form-kemah-rimbun" class="hidden">
                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="nama_penyewa" class="block text-sm font-medium text-gray-700">Nama Penyewa</label>
                            <input type="text" name="nama_penyewa" id="nama_penyewa"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('nama_penyewa') }}" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal
                                    Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('tanggal_mulai') }}" required>
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal
                                    Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('tanggal_selesai') }}" required>
                            </div>
                        </div>
                        <div>
                            <label for="harga_total" class="block text-sm font-medium text-gray-700">Harga Total
                                Sewa</label>
                            <input type="number" name="harga_total" id="harga_total"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('harga_total') }}" min="0" placeholder="Masukkan harga">
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Item Sewa Perlengkapan Camping</h3>
                    <p class="text-sm text-gray-600 mb-4">Pilih perlengkapan yang disewa dan masukkan jumlah serta
                        harganya.</p>

                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-700">Peralatan Tidur & Tempat</h4>
                            <div class="space-y-2 mt-2">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <label class="text-sm font-medium text-gray-700">Tenda</label>
                                    </div>
                                    <div class="pl-6 space-y-2">
                                        <div class="grid grid-cols-3 gap-2 items-center">
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox" name="item[]" value="tenda_2_orang"
                                                    id="item-tenda-2p" data-jumlah="jumlah-tenda-2p"
                                                    data-harga="harga-tenda-2p"
                                                    class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                                <label for="item-tenda-2p"
                                                    class="text-sm font-medium text-gray-700">Kapasitas 2 Orang</label>
                                            </div>
                                            <input type="number" name="jumlah_item[tenda_2_orang]" id="jumlah-tenda-2p"
                                                placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                                value="{{ old('jumlah_item.tenda_2_orang') }}" min="0" disabled>
                                            <input type="number" name="harga_item[tenda_2_orang]" id="harga-tenda-2p"
                                                placeholder="Harga"
                                                class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                                value="{{ old('harga_item.tenda_2_orang') }}" min="0" disabled>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2 items-center">
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox" name="item[]" value="tenda_4_orang"
                                                    id="item-tenda-4p" data-jumlah="jumlah-tenda-4p"
                                                    data-harga="harga-tenda-4p"
                                                    class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                                <label for="item-tenda-4p"
                                                    class="text-sm font-medium text-gray-700">Kapasitas 4 Orang</label>
                                            </div>
                                            <input type="number" name="jumlah_item[tenda_4_orang]" id="jumlah-tenda-4p"
                                                placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                                value="{{ old('jumlah_item.tenda_4_orang') }}" min="0" disabled>
                                            <input type="number" name="harga_item[tenda_4_orang]" id="harga-tenda-4p"
                                                placeholder="Harga"
                                                class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                                value="{{ old('harga_item.tenda_4_orang') }}" min="0" disabled>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2 items-center">
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox" name="item[]" value="tenda_6_orang"
                                                    id="item-tenda-6p" data-jumlah="jumlah-tenda-6p"
                                                    data-harga="harga-tenda-6p"
                                                    class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                                <label for="item-tenda-6p"
                                                    class="text-sm font-medium text-gray-700">Kapasitas 6 Orang</label>
                                            </div>
                                            <input type="number" name="jumlah_item[tenda_6_orang]" id="jumlah-tenda-6p"
                                                placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                                value="{{ old('jumlah_item.tenda_6_orang') }}" min="0" disabled>
                                            <input type="number" name="harga_item[tenda_6_orang]" id="harga-tenda-6p"
                                                placeholder="Harga"
                                                class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                                value="{{ old('harga_item.tenda_6_orang') }}" min="0" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="matras" id="item-matras"
                                            data-jumlah="jumlah-matras" data-harga="harga-matras"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-matras" class="text-sm font-medium text-gray-700">Matras (Busa /
                                            Lipat)</label>
                                    </div>
                                    <input type="number" name="jumlah_item[matras]" id="jumlah-matras"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.matras') }}" min="0" disabled>
                                    <input type="number" name="harga_item[matras]" id="harga-matras"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.matras') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="sleeping_bag"
                                            id="item-sleeping-bag" data-jumlah="jumlah-sleeping-bag"
                                            data-harga="harga-sleeping-bag"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-sleeping-bag" class="text-sm font-medium text-gray-700">Sleeping
                                            Bag</label>
                                    </div>
                                    <input type="number" name="jumlah_item[sleeping_bag]" id="jumlah-sleeping-bag"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.sleeping_bag') }}" min="0" disabled>
                                    <input type="number" name="harga_item[sleeping_bag]" id="harga-sleeping-bag"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.sleeping_bag') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="hammock_parachute"
                                            id="item-hammock" data-jumlah="jumlah-hammock" data-harga="harga-hammock"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-hammock" class="text-sm font-medium text-gray-700">Hammock
                                            Parachute</label>
                                    </div>
                                    <input type="number" name="jumlah_item[hammock_parachute]" id="jumlah-hammock"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.hammock_parachute') }}" min="0" disabled>
                                    <input type="number" name="harga_item[hammock_parachute]" id="harga-hammock"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.hammock_parachute') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="flysheet_tarp" id="item-flysheet"
                                            data-jumlah="jumlah-flysheet" data-harga="harga-flysheet"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-flysheet" class="text-sm font-medium text-gray-700">Flysheet /
                                            Tarp</label>
                                    </div>
                                    <input type="number" name="jumlah_item[flysheet_tarp]" id="jumlah-flysheet"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.flysheet_tarp') }}" min="0" disabled>
                                    <input type="number" name="harga_item[flysheet_tarp]" id="harga-flysheet"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.flysheet_tarp') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="bantal_angin" id="item-bantal"
                                            data-jumlah="jumlah-bantal" data-harga="harga-bantal"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-bantal" class="text-sm font-medium text-gray-700">Bantal
                                            Angin</label>
                                    </div>
                                    <input type="number" name="jumlah_item[bantal_angin]" id="jumlah-bantal"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.bantal_angin') }}" min="0" disabled>
                                    <input type="number" name="harga_item[bantal_angin]" id="harga-bantal"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.bantal_angin') }}" min="0" disabled>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700">Peralatan Masak & Makan</h4>
                            <div class="space-y-2 mt-2">
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="kompor_portable" id="item-kompor"
                                            data-jumlah="jumlah-kompor" data-harga="harga-kompor"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-kompor" class="text-sm font-medium text-gray-700">Kompor
                                            Portable</label>
                                    </div>
                                    <input type="number" name="jumlah_item[kompor_portable]" id="jumlah-kompor"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.kompor_portable') }}" min="0" disabled>
                                    <input type="number" name="harga_item[kompor_portable]" id="harga-kompor"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.kompor_portable') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="nesting_set" id="item-nesting"
                                            data-jumlah="jumlah-nesting" data-harga="harga-nesting"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-nesting" class="text-sm font-medium text-gray-700">Nesting
                                            Set</label>
                                    </div>
                                    <input type="number" name="jumlah_item[nesting_set]" id="jumlah-nesting"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.nesting_set') }}" min="0" disabled>
                                    <input type="number" name="harga_item[nesting_set]" id="harga-nesting"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.nesting_set') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="peralatan_makan" id="item-makan"
                                            data-jumlah="jumlah-makan" data-harga="harga-makan"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-makan" class="text-sm font-medium text-gray-700">Peralatan
                                            Makan</label>
                                    </div>
                                    <input type="number" name="jumlah_item[peralatan_makan]" id="jumlah-makan"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.peralatan_makan') }}" min="0" disabled>
                                    <input type="number" name="harga_item[peralatan_makan]" id="harga-makan"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.peralatan_makan') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="cooler_box" id="item-cooler"
                                            data-jumlah="jumlah-cooler" data-harga="harga-cooler"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-cooler" class="text-sm font-medium text-gray-700">Cooler
                                            Box</label>
                                    </div>
                                    <input type="number" name="jumlah_item[cooler_box]" id="jumlah-cooler"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.cooler_box') }}" min="0" disabled>
                                    <input type="number" name="harga_item[cooler_box]" id="harga-cooler"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.cooler_box') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="barbeque_grill" id="item-bbq"
                                            data-jumlah="jumlah-bbq" data-harga="harga-bbq"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-bbq" class="text-sm font-medium text-gray-700">Barbeque
                                            Grill</label>
                                    </div>
                                    <input type="number" name="jumlah_item[barbeque_grill]" id="jumlah-bbq"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.barbeque_grill') }}" min="0" disabled>
                                    <input type="number" name="harga_item[barbeque_grill]" id="harga-bbq"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.barbeque_grill') }}" min="0" disabled>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700">Api Unggun & Hiburan</h4>
                            <div class="space-y-2 mt-2">
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="paket_api_unggun"
                                            id="item-kayu-bakar" data-jumlah="jumlah-kayu-bakar"
                                            data-harga="harga-kayu-bakar"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-kayu-bakar" class="text-sm font-medium text-gray-700">Paket Api
                                            Unggun (Kayu Bakar)</label>
                                    </div>
                                    <input type="number" name="jumlah_item[paket_api_unggun]" id="jumlah-kayu-bakar"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.paket_api_unggun') }}" min="0" disabled>
                                    <input type="number" name="harga_item[paket_api_unggun]" id="harga-kayu-bakar"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.paket_api_unggun') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="kursi_meja_lipat"
                                            id="item-kursi-meja" data-jumlah="jumlah-kursi-meja"
                                            data-harga="harga-kursi-meja"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-kursi-meja" class="text-sm font-medium text-gray-700">Kursi &
                                            Meja Lipat</label>
                                    </div>
                                    <input type="number" name="jumlah_item[kursi_meja_lipat]" id="jumlah-kursi-meja"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.kursi_meja_lipat') }}" min="0" disabled>
                                    <input type="number" name="harga_item[kursi_meja_lipat]" id="harga-kursi-meja"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.kursi_meja_lipat') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="guitar_ukulele" id="item-guitar"
                                            data-jumlah="jumlah-guitar" data-harga="harga-guitar"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-guitar" class="text-sm font-medium text-gray-700">Guitar /
                                            Ukulele</label>
                                    </div>
                                    <input type="number" name="jumlah_item[guitar_ukulele]" id="jumlah-guitar"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.guitar_ukulele') }}" min="0" disabled>
                                    <input type="number" name="harga_item[guitar_ukulele]" id="harga-guitar"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.guitar_ukulele') }}" min="0" disabled>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700">Penerangan & Keamanan</h4>
                            <div class="space-y-2 mt-2">
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="lentera_camping" id="item-lentera"
                                            data-jumlah="jumlah-lentera" data-harga="harga-lentera"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-lentera" class="text-sm font-medium text-gray-700">Lentera
                                            Camping</label>
                                    </div>
                                    <input type="number" name="jumlah_item[lentera_camping]" id="jumlah-lentera"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.lentera_camping') }}" min="0" disabled>
                                    <input type="number" name="harga_item[lentera_camping]" id="harga-lentera"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.lentera_camping') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="headlamp_senter" id="item-headlamp"
                                            data-jumlah="jumlah-headlamp" data-harga="harga-headlamp"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-headlamp" class="text-sm font-medium text-gray-700">Headlamp /
                                            Senter</label>
                                    </div>
                                    <input type="number" name="jumlah_item[headlamp_senter]" id="jumlah-headlamp"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.headlamp_senter') }}" min="0" disabled>
                                    <input type="number" name="harga_item[headlamp_senter]" id="harga-headlamp"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.headlamp_senter') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="powerbank" id="item-powerbank"
                                            data-jumlah="jumlah-powerbank" data-harga="harga-powerbank"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-powerbank"
                                            class="text-sm font-medium text-gray-700">Powerbank</label>
                                    </div>
                                    <input type="number" name="jumlah_item[powerbank]" id="jumlah-powerbank"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.powerbank') }}" min="0" disabled>
                                    <input type="number" name="harga_item[powerbank]" id="harga-powerbank"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.powerbank') }}" min="0" disabled>
                                </div>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl leading-none">&bull;</span>
                                        <input type="checkbox" name="item[]" value="jas_hujan" id="item-jas-hujan"
                                            data-jumlah="jumlah-jas-hujan" data-harga="harga-jas-hujan"
                                            class="item-checkbox rounded border-gray-300 text-teal-600 shadow-sm">
                                        <label for="item-jas-hujan" class="text-sm font-medium text-gray-700">Jas
                                            Hujan</label>
                                    </div>
                                    <input type="number" name="jumlah_item[jas_hujan]" id="jumlah-jas-hujan"
                                        placeholder="Jumlah" class="w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_item.jas_hujan') }}" min="0" disabled>
                                    <input type="number" name="harga_item[jas_hujan]" id="harga-jas-hujan"
                                        placeholder="Harga" class="w-full rounded-md border-gray-300 shadow-sm item-harga"
                                        value="{{ old('harga_item.jas_hujan') }}" min="0" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
    <label for="subtotal_item" class="col-sm-4 col-form-label text-right">Subtotal</label>
    <div class="col-sm-8">
        <input type="text" class="form-control" id="subtotal_item" readonly>
    </div>
</div>

<input type="hidden" name="total_pendapatan" id="total_pendapatan">

                    <div class="mt-6">
                        <label for="deskripsi_kemah" class="block text-sm font-medium text-gray-700">Deskripsi
                            (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_kemah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>
                
                {{-- Form untuk Sewa Tempat --}}
                <div id="form-sewa-tempat" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_penyewa" class="block text-sm font-medium text-gray-700">Nama Penyewa</label>
                            <input type="text" name="nama_penyewa" id="nama_penyewa"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('nama_penyewa') }}" disabled>
                        </div>
                        <div>
    <label for="jumlah_peserta" class="block text-sm font-medium text-gray-700">
        Jumlah Peserta/Pengunjung
    </label>
    <input type="number" name="jumlah_peserta" id="jumlah_peserta"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
        placeholder="Minimal 50 orang"
        value="{{ old('jumlah_peserta') }}" min="1" disabled>
</div>
                        <div>
                            <label for="harga_paket_display" class="block text-sm font-medium text-gray-700">Harga Paket
                                (Kuota 50 Orang)</label>
                            <input type="text" id="harga_paket_display"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100"
                                value="Rp 350.000" readonly disabled>
                        </div>
                        <div>
                            <label for="harga_awal_sewa_tempat_display"
                                class="block text-sm font-medium text-gray-700">Harga Awal</label>
                            <input type="text" id="harga_awal_sewa_tempat_display"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" value="0"
                                readonly disabled>
                            <input type="hidden" name="harga_awal_sewa_tempat" id="harga_awal_sewa_tempat_hidden">
                        </div>
                        <div>
                            <label for="diskon" class="block text-sm font-medium text-gray-700">Diskon (%)</label>
                            <input type="number" name="diskon" id="diskon"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('diskon', 0) }}" min="0" max="100" disabled>
                        </div>
                        <div>
                            <label for="harga_akhir_sewa_tempat_display"
                                class="block text-sm font-medium text-gray-700">Harga Akhir</label>
                            <input type="text" id="harga_akhir_sewa_tempat_display"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" value="0"
                                readonly disabled>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="deskripsi_sewa" class="block text-sm font-medium text-gray-700">Deskripsi
                            (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_sewa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            disabled>{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                {{-- Form untuk Seluncuran --}}
                <div id="form-seluncuran" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tiket_terjual" class="block text-sm font-medium text-gray-700">Tiket
                                Terjual</label>
                            <input type="number" name="tiket_terjual" id="tiket_terjual"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('tiket_terjual') }}" disabled>
                        </div>
                        <div>
                            <label for="harga_tiket" class="block text-sm font-medium text-gray-700">Harga Tiket</label>
                            <input type="number" name="harga_tiket" id="harga_tiket"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('harga_tiket') }}" disabled>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="deskripsi_seluncuran" class="block text-sm font-medium text-gray-700">Deskripsi
                            (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_seluncuran" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            disabled>{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                {{-- Form untuk ATV --}}
                <div id="form-atv" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="jumlah_sewa" class="block text-sm font-medium text-gray-700">Jumlah Sewa</label>
                            <input type="number" name="jumlah_sewa" id="jumlah_sewa"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('jumlah_sewa') }}" disabled>
                        </div>
                        <div>
                            <label for="durasi_sewa" class="block text-sm font-medium text-gray-700">Durasi (Jam)</label>
                            <input type="number" name="durasi_sewa" id="durasi_sewa"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('durasi_sewa') }}" disabled>
                        </div>
                        <div>
                            <label for="tarif_sewa" class="block text-sm font-medium text-gray-700">Tarif (Harga)</label>
                            <input type="number" name="tarif_sewa" id="tarif_sewa"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('tarif_sewa') }}" disabled>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="deskripsi_atv" class="block text-sm font-medium text-gray-700">Deskripsi
                            (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_atv" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            disabled>{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                {{-- Form untuk Parkir --}}
                <div id="form-parkir" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700">Jenis
                                Kendaraan</label>
                            <select name="jenis_kendaraan" id="jenis_kendaraan"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled>
                                <option value="">Pilih Jenis Kendaraan</option>
                                <option value="motor">Motor</option>
                                <option value="mobil">Mobil</option>
                                <option value="bis">Bis</option>
                                <option value="travel">Travel</option>
                            </select>
                        </div>

                        {{-- Fields untuk Motor --}}
                        <div id="form-motor-fields" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="jumlah_motor" class="block text-sm font-medium text-gray-700">Jumlah
                                    Motor</label>
                                <input type="number" name="jumlah_motor" id="jumlah_motor"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('jumlah_motor', 0) }}" min="0" disabled>
                            </div>
                            <div>
                                <label for="harga_motor" class="block text-sm font-medium text-gray-700">Harga Tiket
                                    Motor</label>
                                <input type="number" name="harga_motor" id="harga_motor"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('harga_motor') }}" disabled>
                            </div>
                        </div>

                        {{-- Fields untuk Mobil --}}
                        <div id="form-mobil-fields" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="jumlah_mobil" class="block text-sm font-medium text-gray-700">Jumlah
                                    Mobil</label>
                                <input type="number" name="jumlah_mobil" id="jumlah_mobil"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('jumlah_mobil', 0) }}" min="0" disabled>
                            </div>
                            <div>
                                <label for="harga_mobil" class="block text-sm font-medium text-gray-700">Harga Tiket
                                    Mobil</label>
                                <input type="number" name="harga_mobil" id="harga_mobil"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('harga_mobil') }}" disabled>
                            </div>
                        </div>

                        {{-- Fields untuk Bis --}}
                        <div id="form-bis-fields" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="jumlah_bis" class="block text-sm font-medium text-gray-700">Jumlah Bis</label>
                                <input type="number" name="jumlah_bis" id="jumlah_bis"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('jumlah_bis', 0) }}" min="0" disabled>
                            </div>
                            <div>
                                <label for="harga_bis" class="block text-sm font-medium text-gray-700">Harga Tiket
                                    Bis</label>
                                <input type="number" name="harga_bis" id="harga_bis"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('harga_bis') }}" disabled>
                            </div>
                        </div>

                        {{-- Fields untuk Travel --}}
                        <div id="form-travel-fields" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="jumlah_travel" class="block text-sm font-medium text-gray-700">Jumlah
                                    Travel</label>
                                <input type="number" name="jumlah_travel" id="jumlah_travel"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('jumlah_travel', 0) }}" min="0" disabled>
                            </div>
                            <div>
                                <label for="harga_travel" class="block text-sm font-medium text-gray-700">Harga Tiket
                                    Travel</label>
                                <input type="number" name="harga_travel" id="harga_travel"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('harga_travel') }}" disabled>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="total_parkir_display"
                                class="block text-sm font-medium text-gray-700">Total</label>
                            <input type="text" id="total_parkir_display"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" value="0"
                                readonly disabled>
                            <input type="hidden" name="total_parkir" id="total_parkir_hidden">
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="deskripsi_parkir" class="block text-sm font-medium text-gray-700">Deskripsi
                            (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi_parkir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            disabled>{{ old('deskripsi') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded">Simpan
                    Transaksi</button>
            </div>
        </form>
    </div>
@endsection

<script>
    const groupedBarangs = @json($groupedBarangs);
    const hargaDasarSewa = @json($hargaDasarSewa);

    document.addEventListener('DOMContentLoaded', function() {
        const unitSelect = document.getElementById('unit_id');
        const forms = {
            '1': document.getElementById('form-cafe'),
            '2': document.getElementById('form-sewa-tempat'),
            '3': document.getElementById('form-seluncuran'),
            '4': document.getElementById('form-atv'),
            '5': document.getElementById('form-parkir'),
            '6': document.getElementById('form-kemah-rimbun'),
        };

        // --- Show/hide form ---
        function showForm(unitId) {
            Object.values(forms).forEach(form => {
                form.classList.add('hidden');
                form.querySelectorAll('input, select, textarea').forEach(input => {
                    input.setAttribute('disabled', 'disabled');
                });
            });

            if (forms[unitId]) {
                forms[unitId].classList.remove('hidden');
                forms[unitId].querySelectorAll('input, select, textarea, button').forEach(input => {
                    input.removeAttribute('disabled');
                });

                if (unitId === '5') {
                    const jenisKendaraanSelect = document.getElementById('jenis_kendaraan');
                    handleParkingForm(jenisKendaraanSelect.value);
                }
            }
        }

        // --- Parkir ---
        function handleParkingForm(jenis) {
            const vehicleFields = {
                'motor': document.getElementById('form-motor-fields'),
                'mobil': document.getElementById('form-mobil-fields'),
                'bis': document.getElementById('form-bis-fields'),
                'travel': document.getElementById('form-travel-fields'),
            };

            const vehicleInputs = {
                'motor': { jumlah: document.getElementById('jumlah_motor'), harga: document.getElementById('harga_motor') },
                'mobil': { jumlah: document.getElementById('jumlah_mobil'), harga: document.getElementById('harga_mobil') },
                'bis': { jumlah: document.getElementById('jumlah_bis'), harga: document.getElementById('harga_bis') },
                'travel': { jumlah: document.getElementById('jumlah_travel'), harga: document.getElementById('harga_travel') },
            };

            Object.values(vehicleFields).forEach(fields => fields.classList.add('hidden'));
            Object.values(vehicleInputs).forEach(inputs => {
                inputs.jumlah.setAttribute('disabled', 'disabled');
                inputs.harga.setAttribute('disabled', 'disabled');
                inputs.jumlah.value = 0;
                inputs.harga.value = '';
            });

            if (jenis && vehicleFields[jenis]) {
                vehicleFields[jenis].classList.remove('hidden');
                vehicleInputs[jenis].jumlah.removeAttribute('disabled');
                vehicleInputs[jenis].harga.removeAttribute('disabled');
            }

            calculateTotalParkir();
        }

        function calculateTotalParkir() {
            const total =
                (parseFloat(document.getElementById('jumlah_motor').value) || 0) * (parseFloat(document.getElementById('harga_motor').value) || 0) +
                (parseFloat(document.getElementById('jumlah_mobil').value) || 0) * (parseFloat(document.getElementById('harga_mobil').value) || 0) +
                (parseFloat(document.getElementById('jumlah_bis').value) || 0) * (parseFloat(document.getElementById('harga_bis').value) || 0) +
                (parseFloat(document.getElementById('jumlah_travel').value) || 0) * (parseFloat(document.getElementById('harga_travel').value) || 0);

            document.getElementById('total_parkir_display').value = total.toFixed(0);
            document.getElementById('total_parkir_hidden').value = total.toFixed(0);
        }

        const jenisKendaraanSelect = document.getElementById('jenis_kendaraan');
        if (jenisKendaraanSelect) {
            jenisKendaraanSelect.addEventListener('change', function() {
                handleParkingForm(this.value);
            });
            ['jumlah_motor','harga_motor','jumlah_mobil','harga_mobil','jumlah_bis','harga_bis','jumlah_travel','harga_travel'].forEach(id => {
                const input = document.getElementById(id);
                if (input) input.addEventListener('input', calculateTotalParkir);
            });
        }

        // --- Update item Cafe ---
        function updateItemOptions(unitId, selectElement = null, selectedValue = null) {
            if (unitId !== '1') return;
            const barangList = groupedBarangs[unitId] || [];
            const renderOptions = (select) => {
                select.innerHTML = '<option value="">Pilih Barang</option>';
                barangList.forEach(barang => {
                    const option = document.createElement('option');
                    option.value = barang.id_barang;
                    option.textContent = barang.nama_barang;
                    if (selectedValue && selectedValue == barang.id_barang) option.selected = true;
                    select.appendChild(option);
                });
            };
            if (selectElement) renderOptions(selectElement);
            else document.querySelectorAll('.item-select').forEach(renderOptions);
        }

        function addItem() {
            const itemList = document.getElementById('item-list');
            const newIndex = itemList.querySelectorAll('.item-row').length;
            const newItemRow = document.createElement('div');
            newItemRow.classList.add('item-row','flex','items-center','gap-4','mb-4');
            newItemRow.innerHTML = `
                <select name="items[${newIndex}][id_barang]" class="flex-1 rounded-md border-gray-300 shadow-sm item-select" required>
                    <option value="">Pilih Barang</option>
                </select>
                <input type="number" name="items[${newIndex}][jumlah]" class="w-24 rounded-md border-gray-300 shadow-sm" value="1" min="1" required>
                <button type="button" class="text-red-500 hover:text-red-700 font-bold" onclick="window.removeItem(this)">Hapus</button>
            `;
            itemList.appendChild(newItemRow);
            updateItemOptions(unitSelect.value, newItemRow.querySelector('select'));
        }

        window.removeItem = function(button) {
            button.closest('.item-row').remove();
            document.querySelectorAll('#item-list .item-row').forEach((row, index) => {
                row.querySelector('select').name = `items[${index}][id_barang]`;
                row.querySelector('input').name = `items[${index}][jumlah]`;
            });
        };

        // --- Sewa Tempat ---
        const jumlahPesertaInput = document.getElementById('jumlah_peserta');
        const diskonInput = document.getElementById('diskon');
        function calculateSewaTempat() {
            const jumlahPeserta = parseFloat(jumlahPesertaInput.value) || 0;
            const diskon = parseFloat(diskonInput.value) || 0;
            const hargaPaket50Orang = 350000;
            const kuotaPaket = 50;
            let hargaAwal = jumlahPeserta > kuotaPaket ? jumlahPeserta * (hargaPaket50Orang / kuotaPaket) : (jumlahPeserta > 0 ? hargaPaket50Orang : 0);
            const hargaAkhir = hargaAwal - (hargaAwal * (diskon / 100));
            document.getElementById('harga_awal_sewa_tempat_display').value = hargaAwal.toLocaleString('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0});
            document.getElementById('harga_akhir_sewa_tempat_display').value = hargaAkhir.toLocaleString('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0});
            document.getElementById('harga_awal_sewa_tempat_hidden').value = hargaAwal;
        }
        if (jumlahPesertaInput && diskonInput) {
            jumlahPesertaInput.addEventListener('input', calculateSewaTempat);
            diskonInput.addEventListener('input', calculateSewaTempat);
        }

        // --- Kemah Rimbun ---
        const formKemahRimbun = document.getElementById('form-kemah-rimbun');
        if (formKemahRimbun) {
            const checkboxes = formKemahRimbun.querySelectorAll('.item-checkbox');
            const subtotalItemInput = document.getElementById('subtotal_item');
            const hargaTotalInput = document.getElementById('harga_total');
            const totalPendapatanInput = document.getElementById('total_pendapatan');

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format(number);
            }

            function calculateSubtotal() {
                let totalItem = 0;
                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        const jumlahInput = document.getElementById(cb.dataset.jumlah);
                        const hargaInput = document.getElementById(cb.dataset.harga);
                        const jml = parseInt(jumlahInput.value) || 0;
                        const hrg = parseInt(hargaInput.value) || 0;
                        totalItem += jml * hrg;
                    }
                });
                const hargaSewa = parseInt(hargaTotalInput.value) || 0;
                const finalTotal = totalItem + hargaSewa;

                subtotalItemInput.value = formatRupiah(finalTotal);
                totalPendapatanInput.value = finalTotal; // angka murni dikirim ke server
            }

            checkboxes.forEach(cb => {
                const jumlahInput = document.getElementById(cb.dataset.jumlah);
                const hargaInput = document.getElementById(cb.dataset.harga);
                cb.addEventListener('change', function() {
                    if (this.checked) {
                        jumlahInput.disabled = false;
                        hargaInput.disabled = false;
                    } else {
                        jumlahInput.disabled = true;
                        hargaInput.disabled = true;
                        jumlahInput.value = '';
                        hargaInput.value = '';
                    }
                    calculateSubtotal();
                });
                jumlahInput.addEventListener('input', calculateSubtotal);
                hargaInput.addEventListener('input', calculateSubtotal);
            });

            hargaTotalInput.addEventListener('input', calculateSubtotal);
            calculateSubtotal();
        }

        // --- Init ---
        const oldUnitId = '{{ old('id_unit') }}';
        if (oldUnitId) {
            showForm(oldUnitId);
            updateItemOptions(oldUnitId);
        } else {
            showForm(unitSelect.value);
            updateItemOptions(unitSelect.value);
        }

        unitSelect.addEventListener('change', function() {
            showForm(this.value);
            updateItemOptions(this.value);
        });

        window.addItem = addItem;
    });
</script>