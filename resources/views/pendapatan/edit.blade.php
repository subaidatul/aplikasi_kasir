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
                <input type="text" value="{{ $pendapatan->unit->nama_unit ?? 'N/A' }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 cursor-not-allowed leading-tight focus:outline-none focus:shadow-outline"
                    disabled>
                <input type="hidden" name="id_unit" id="id_unit" value="{{ $pendapatan->id_unit }}">
            </div>

            <div class="mb-4">
                <label for="tanggal" class="block text-gray-700 text-sm font-bold mb-2">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal"
                    value="{{ old('tanggal', \Carbon\Carbon::parse($pendapatan->tanggal)->format('Y-m-d')) }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>

            {{-- Logika Blade untuk menampilkan form input spesifik berdasarkan Unit ID --}}
            @switch($pendapatan->id_unit)
                @case(1)
                    {{-- Cafe/Wifi --}}
                    <div id="form-cafe">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Item Pendapatan</h3>
                        <div id="item-list">
                            @foreach ($pendapatan->detailPendapatan as $index => $detail)
                                <div class="item-row flex items-center gap-4 mb-4">
                                    <select name="items[{{ $index }}][id_barang]"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm item-select" required>
                                        <option value="">Pilih Barang</option>
                                        @foreach ($groupedBarangs[1] as $barang)
                                            <option value="{{ $barang->id_barang }}"
                                                {{ $detail->id_barang == $barang->id_barang ? 'selected' : '' }}>
                                                {{ $barang->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="items[{{ $index }}][jumlah]"
                                        class="w-24 rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('items.' . $index . '.jumlah', $detail->jumlah) }}" min="1" required>
                                    <button type="button" onclick="removeItem(this)"
                                        class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" onclick="addItem()" class="text-blue-500 hover:text-blue-700 font-bold mb-4">+ Tambah
                            Item</button>
                    </div>
                @break

                @case(2)
                    {{-- Sewa Tempat --}}
                    @php
                        $namaPenyewa = '';
                        if ($pendapatan->deskripsi && str_contains($pendapatan->deskripsi, ' atas nama ')) {
                            $parts = explode(' atas nama ', $pendapatan->deskripsi, 2);
                            $namaPenyewaRaw = $parts[1] ?? '';
                            $firstDotPos = strpos($namaPenyewaRaw, '. ');
                            if ($firstDotPos !== false) {
                                $namaPenyewa = substr($namaPenyewaRaw, 0, $firstDotPos);
                            } else {
                                $namaPenyewa = $namaPenyewaRaw;
                            }
                        }
                    @endphp
                    <div id="form-sewa-tempat">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama_penyewa" class="block text-sm font-medium text-gray-700">Nama Penyewa</label>
                                <input type="text" name="nama_penyewa" id="nama_penyewa"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('nama_penyewa', $namaPenyewa) }}" required>
                            </div>
                            <div>
                                <label for="harga_akhir" class="block text-sm font-medium text-gray-700">Total Harga</label>
                                <input type="number" name="harga_akhir" id="harga_akhir"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('harga_akhir', $pendapatan->total) }}" min="0" required>
                            </div>
                        </div>
                    </div>
                @break

                @case(3)
                    {{-- Seluncuran --}}
                    @php
                        preg_match('/Tiket Terjual: (\d+).*Harga Tiket: (\d+)/', $pendapatan->deskripsi, $matches);
                        $tiketTerjual = $matches[1] ?? 0;
                        $hargaTiket = $matches[2] ?? 0;
                        $deskripsi_display = preg_replace(
                            '/Tiket Terjual: \d+, Harga Tiket: \d+\.?/',
                            '',
                            $pendapatan->deskripsi,
                        );
                    @endphp
                    <div id="form-seluncuran">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tiket_terjual" class="block text-sm font-medium text-gray-700">Tiket Terjual</label>
                                <input type="number" name="tiket_terjual" id="tiket_terjual"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('tiket_terjual', $tiketTerjual) }}" required>
                            </div>
                            <div>
                                <label for="harga_tiket" class="block text-sm font-medium text-gray-700">Harga Tiket</label>
                                <input type="number" name="harga_tiket" id="harga_tiket"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('harga_tiket', $hargaTiket) }}" required>
                            </div>
                        </div>
                    </div>
                @break

                @case(4)
                    {{-- ATV --}}
                    @php
                        preg_match('/Jumlah Sewa: (\d+)/', $pendapatan->deskripsi, $jumlahSewaMatch);
                        preg_match('/Durasi: (\d+)/', $pendapatan->deskripsi, $durasiSewaMatch);
                        preg_match('/Tarif: (\d+)/', $pendapatan->deskripsi, $tarifSewaMatch);
                        $jumlahSewa = $jumlahSewaMatch[1] ?? 0;
                        $durasiSewa = $durasiSewaMatch[1] ?? 0;
                        $tarifSewa = $tarifSewaMatch[1] ?? 0;
                        $deskripsi_display = preg_replace(
                            '/Jumlah Sewa: \d+, Durasi: \d+, Tarif: \d+\.?/',
                            '',
                            $pendapatan->deskripsi,
                        );
                    @endphp
                    <div id="form-atv">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="jumlah_sewa" class="block text-sm font-medium text-gray-700">Jumlah Sewa</label>
                                <input type="number" name="jumlah_sewa" id="jumlah_sewa"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('jumlah_sewa', $jumlahSewa) }}" required>
                            </div>
                            <div>
                                <label for="durasi_sewa" class="block text-sm font-medium text-gray-700">Durasi (Jam)</label>
                                <input type="number" name="durasi_sewa" id="durasi_sewa"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('durasi_sewa', $durasiSewa) }}" required>
                            </div>
                            <div>
                                <label for="tarif_sewa" class="block text-sm font-medium text-gray-700">Tarif (Harga)</label>
                                <input type="number" name="tarif_sewa" id="tarif_sewa"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('tarif_sewa', $tarifSewa) }}" required>
                            </div>
                        </div>
                    </div>
                @break

               @case(5)
                    {{-- Parkir --}}
                    @php
                        // Inisialisasi default values
                        $jenis_kendaraan = ''; // Akan menyimpan 'motor', 'mobil', 'bis', 'travel'
                        $jumlah_motor = 0;
                        $harga_motor = 0;
                        $jumlah_mobil = 0;
                        $harga_mobil = 0;
                        $jumlah_bis = 0;
                        $harga_bis = 0;
                        $jumlah_travel = 0;
                        $harga_travel = 0;
                        
                        $deskripsi_raw = $pendapatan->deskripsi;
                        $deskripsi_display = $deskripsi_raw;

                        // Ekstraksi data dari deskripsi
                        if (str_contains($deskripsi_raw, 'Jenis Kendaraan: Motor')) {
                            $jenis_kendaraan = 'motor';
                            preg_match('/Jumlah Motor: (\d+)/', $deskripsi_raw, $jumlahMotorMatch);
                            preg_match('/Harga Motor: (\d+)/', $deskripsi_raw, $hargaMotorMatch); // Pastikan nama field harga di deskripsi juga konsisten
                            $jumlah_motor = $jumlahMotorMatch[1] ?? 0;
                            $harga_motor = $hargaMotorMatch[1] ?? 0;
                            $deskripsi_display = preg_replace('/Jenis Kendaraan: Motor\. Jumlah Motor: \d+, Harga Motor: \d+\.?/', '', $deskripsi_display);
                        } elseif (str_contains($deskripsi_raw, 'Jenis Kendaraan: Mobil')) {
                            $jenis_kendaraan = 'mobil';
                            preg_match('/Jumlah Mobil: (\d+)/', $deskripsi_raw, $jumlahMobilMatch);
                            preg_match('/Harga Mobil: (\d+)/', $deskripsi_raw, $hargaMobilMatch);
                            $jumlah_mobil = $jumlahMobilMatch[1] ?? 0;
                            $harga_mobil = $hargaMobilMatch[1] ?? 0;
                            $deskripsi_display = preg_replace('/Jenis Kendaraan: Mobil\. Jumlah Mobil: \d+, Harga Mobil: \d+\.?/', '', $deskripsi_display);
                        } elseif (str_contains($deskripsi_raw, 'Jenis Kendaraan: Bis')) {
                            $jenis_kendaraan = 'bis';
                            preg_match('/Jumlah Bis: (\d+)/', $deskripsi_raw, $jumlahBisMatch);
                            preg_match('/Harga Bis: (\d+)/', $deskripsi_raw, $hargaBisMatch);
                            $jumlah_bis = $jumlahBisMatch[1] ?? 0;
                            $harga_bis = $hargaBisMatch[1] ?? 0;
                            $deskripsi_display = preg_replace('/Jenis Kendaraan: Bis\. Jumlah Bis: \d+, Harga Bis: \d+\.?/', '', $deskripsi_display);
                        } elseif (str_contains($deskripsi_raw, 'Jenis Kendaraan: Travel')) {
                            $jenis_kendaraan = 'travel';
                            preg_match('/Jumlah Travel: (\d+)/', $deskripsi_raw, $jumlahTravelMatch);
                            preg_match('/Harga Travel: (\d+)/', $deskripsi_raw, $hargaTravelMatch);
                            $jumlah_travel = $jumlahTravelMatch[1] ?? 0;
                            $harga_travel = $hargaTravelMatch[1] ?? 0;
                            $deskripsi_display = preg_replace('/Jenis Kendaraan: Travel\. Jumlah Travel: \d+, Harga Travel: \d+\.?/', '', $deskripsi_display);
                        }
                        // Bersihkan sisa deskripsi
                        $deskripsi_display = trim($deskripsi_display);
                    @endphp
                    <div id="form-parkir">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="jenis_kendaraan_parkir" class="block text-sm font-medium text-gray-700">Jenis Kendaraan</label>
                                <select name="jenis_kendaraan" id="jenis_kendaraan_parkir"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Pilih Jenis Kendaraan</option>
                                    <option value="motor" {{ $jenis_kendaraan == 'motor' ? 'selected' : '' }}>Motor</option>
                                    <option value="mobil" {{ $jenis_kendaraan == 'mobil' ? 'selected' : '' }}>Mobil</option>
                                    <option value="bis" {{ $jenis_kendaraan == 'bis' ? 'selected' : '' }}>Bis</option>
                                    <option value="travel" {{ $jenis_kendaraan == 'travel' ? 'selected' : '' }}>Travel</option>
                                </select>
                            </div>
                            
                            {{-- Field untuk Motor --}}
                            <div id="form-motor-fields"
                                class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 {{ $jenis_kendaraan == 'motor' ? '' : 'hidden' }}">
                                <div>
                                    <label for="jumlah_motor" class="block text-sm font-medium text-gray-700">Jumlah Motor</label>
                                    <input type="number" name="jumlah_motor" id="jumlah_motor"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_motor', $jumlah_motor) }}" min="0">
                                </div>
                                <div>
                                    <label for="harga_motor" class="block text-sm font-medium text-gray-700">Harga Motor</label>
                                    <input type="number" name="harga_motor" id="harga_motor"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('harga_motor', $harga_motor) }}">
                                </div>
                            </div>

                            {{-- Field untuk Mobil --}}
                            <div id="form-mobil-fields"
                                class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 {{ $jenis_kendaraan == 'mobil' ? '' : 'hidden' }}">
                                <div>
                                    <label for="jumlah_mobil" class="block text-sm font-medium text-gray-700">Jumlah Mobil</label>
                                    <input type="number" name="jumlah_mobil" id="jumlah_mobil"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_mobil', $jumlah_mobil) }}" min="0">
                                </div>
                                <div>
                                    <label for="harga_mobil" class="block text-sm font-medium text-gray-700">Harga Mobil</label>
                                    <input type="number" name="harga_mobil" id="harga_mobil"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('harga_mobil', $harga_mobil) }}">
                                </div>
                            </div>
                            
                            {{-- Field untuk Bis --}}
                            <div id="form-bis-fields"
                                class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 {{ $jenis_kendaraan == 'bis' ? '' : 'hidden' }}">
                                <div>
                                    <label for="jumlah_bis" class="block text-sm font-medium text-gray-700">Jumlah Bis</label>
                                    <input type="number" name="jumlah_bis" id="jumlah_bis"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_bis', $jumlah_bis) }}" min="0">
                                </div>
                                <div>
                                    <label for="harga_bis" class="block text-sm font-medium text-gray-700">Harga Bis</label>
                                    <input type="number" name="harga_bis" id="harga_bis"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('harga_bis', $harga_bis) }}">
                                </div>
                            </div>

                            {{-- Field untuk Travel --}}
                            <div id="form-travel-fields"
                                class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 {{ $jenis_kendaraan == 'travel' ? '' : 'hidden' }}">
                                <div>
                                    <label for="jumlah_travel" class="block text-sm font-medium text-gray-700">Jumlah Travel</label>
                                    <input type="number" name="jumlah_travel" id="jumlah_travel"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('jumlah_travel', $jumlah_travel) }}" min="0">
                                </div>
                                <div>
                                    <label for="harga_travel" class="block text-sm font-medium text-gray-700">Harga Travel</label>
                                    <input type="number" name="harga_travel" id="harga_travel"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        value="{{ old('harga_travel', $harga_travel) }}">
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label for="total_parkir_display" class="block text-sm font-medium text-gray-700">Total</label>
                                <input type="text" id="total_parkir_display"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100"
                                    value="{{ old('total_parkir', $pendapatan->total) }}" readonly>
                                <input type="hidden" name="total_parkir" id="total_parkir_hidden"
                                    value="{{ old('total_parkir', $pendapatan->total) }}">
                            </div>
                        </div>
                    </div>
                @break

             {{-- ✅ BLOK KEMAH RIMBUN YANG SUDAH DIREVISI SESUAI CREATE --}}
@case(6)
    {{-- 1. Field Nama Penyewa --}}
    <div class="mb-4">
        <label for="nama_penyewa" class="block text-gray-700 text-sm font-bold mb-2">Nama Penyewa</label>
        <input type="text" name="nama_penyewa" id="nama_penyewa"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            value="{{ old('nama_penyewa', $namaPenyewaLahan ?? '') }}" 
            placeholder="Masukkan Nama Penyewa"
            required>
        @error('nama_penyewa')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>

    {{-- 2. Field Tanggal Mulai Sewa --}}
    <div class="mb-4">
        <label for="tanggal_mulai" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai Sewa</label>
        <input type="date" name="tanggal_mulai" id="tanggal_mulai"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            value="{{ old('tanggal_mulai', $tanggalMulai ?? '') }}"
            required>
        @error('tanggal_mulai')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>

    {{-- 3. Field Tanggal Selesai Sewa --}}
    <div class="mb-4">
        <label for="tanggal_selesai" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Selesai Sewa</label>
        <input type="date" name="tanggal_selesai" id="tanggal_selesai"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            value="{{ old('tanggal_selesai', $tanggalSelesai ?? '') }}"
            required>
        @error('tanggal_selesai')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>

   

    {{-- 5. Daftar Item Manual (Perlengkapan Camping) --}}
    <div id="form-kemah-rimbun">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Item Pendapatan (Kemah Rimbun)</h3>
        <div id="item-list-kemah">
            @forelse ($itemPerlengkapan ?? $pendapatan->detailPendapatan as $index => $detail)
                @php
                    $namaItem = is_array($detail) ? ($detail['nama'] ?? '') : ($detail->nama_barang_manual ?? $detail->nama_item);
                    $jumlahItem = is_array($detail) ? ($detail['jumlah'] ?? '') : ($detail->jumlah ?? '');
                    $hargaItem = is_array($detail) ? ($detail['harga'] ?? '') : ($detail->harga ?? $detail->harga_per_item);
                @endphp
                <div class="item-row-kemah flex items-center gap-4 mb-4">
                    <input type="text" name="item_manual[{{ $index }}][nama]" 
                        class="flex-1 rounded-md border-gray-300 shadow-sm"
                        placeholder="Nama Item"
                        value="{{ old("item_manual.$index.nama", $namaItem) }}" required>
                    
                    <input type="number" name="item_manual[{{ $index }}][jumlah]" 
                        class="w-24 rounded-md border-gray-300 shadow-sm"
                        placeholder="Jumlah"
                        value="{{ old("item_manual.$index.jumlah", $jumlahItem) }}" min="1" required>
                    
                    <input type="number" name="item_manual[{{ $index }}][harga]" 
                        class="w-24 rounded-md border-gray-300 shadow-sm item-kemah-price"
                        placeholder="Harga"
                        value="{{ old("item_manual.$index.harga", $hargaItem) }}" min="0" required>
                    
                    <button type="button" onclick="removeItemKemah(this)" 
                        class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                </div>
            @empty
                {{-- Jika belum ada item sama sekali --}}
                <div class="item-row-kemah flex items-center gap-4 mb-4">
                    <input type="text" name="item_manual[0][nama]" class="flex-1 rounded-md border-gray-300 shadow-sm"
                        placeholder="Nama Item" required>
                    <input type="number" name="item_manual[0][jumlah]" class="w-24 rounded-md border-gray-300 shadow-sm"
                        placeholder="Jumlah" min="1" required>
                    <input type="number" name="item_manual[0][harga]" class="w-24 rounded-md border-gray-300 shadow-sm item-kemah-price"
                        placeholder="Harga" min="0" required>
                    <button type="button" onclick="removeItemKemah(this)"
                        class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                </div>
            @endforelse
        </div>

        <button type="button" onclick="addItemKemah()" 
            class="text-blue-500 hover:text-blue-700 font-bold mb-4">+ Tambah Item</button>
    </div>

    {{-- 6. Field Total Harga --}}
    <div class="mb-4">
        <label for="total_harga_display" class="block text-gray-700 text-sm font-bold mb-2">Total Pendapatan</label>
        <input type="text" id="total_harga_display" 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-100 font-bold" 
            value="{{ number_format($pendapatan->total_harga ?? 0, 0, ',', '.') }}" disabled>
        <input type="hidden" name="harga_total" id="total_harga_hidden" 
            value="{{ old('harga_total', $pendapatan->total_harga ?? 0) }}">
    </div>
@break
            @endswitch

            <div class="mb-4 mt-6">
                <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('deskripsi', $deskripsi_display ?? $pendapatan->deskripsi) }}</textarea>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Pendapatan
                </button>
                <a href="{{ route('pendapatan.index') }}"
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- ✅ SCRIPT FUNGSI DINAMIS YANG SUDAH DIBERSIHKAN & DITINGKATKAN --}}
<script>
    const groupedBarangs = @json($groupedBarangs ?? []);
    let itemCounter = {{ $pendapatan->id_unit == 1 && $pendapatan->detailPendapatan ? $pendapatan->detailPendapatan->count() : 0 }};
    let kemahItemCounter = {{ $pendapatan->id_unit == 6 && $pendapatan->detailPendapatan ? $pendapatan->detailPendapatan->count() : 0 }};

    /* =========================
       🔹 FUNGSI TOTAL KEMAH RIMBUN
       ========================= */
    function calculateTotalKemah() {
        const itemRows = document.querySelectorAll('#item-list-kemah .item-row-kemah');
        let grandTotal = 0;

        itemRows.forEach(row => {
            const jumlah = parseFloat(row.querySelector('input[name*="[jumlah]"]')?.value || 0);
            const harga  = parseFloat(row.querySelector('input[name*="[harga]"]')?.value || 0);
            grandTotal += jumlah * harga;
        });

        const totalHidden = document.getElementById('total_harga_hidden');
        const totalDisplay = document.getElementById('total_harga_display');

        if (totalHidden) totalHidden.value = grandTotal.toFixed(0);
        if (totalDisplay)
            totalDisplay.value = 'Rp ' + grandTotal.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    /* =========================
       🔹 FUNGSI KAFE (ID 1)
       ========================= */
    function updateItemOptions(selectElement) {
        const unitId = document.getElementById('id_unit')?.value;
        const selectedItemId = selectElement.value;
        selectElement.innerHTML = '<option value="">Pilih Barang</option>';

        if (groupedBarangs[unitId]) {
            groupedBarangs[unitId].forEach(barang => {
                const option = document.createElement('option');
                option.value = barang.id_barang;
                option.textContent = barang.nama_barang;
                if (barang.id_barang == selectedItemId) option.selected = true;
                selectElement.appendChild(option);
            });
        }
    }

    function addItem() {
        const itemList = document.getElementById('item-list');
        if (!itemList) return;

        const newItemRow = document.createElement('div');
        newItemRow.className = 'item-row flex items-center gap-4 mb-4';
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
        document.querySelectorAll('#item-list .item-row').forEach((row, index) => {
            row.querySelector('select').name = `items[${index}][id_barang]`;
            row.querySelector('input[type="number"]').name = `items[${index}][jumlah]`;
        });
        itemCounter = document.querySelectorAll('#item-list .item-row').length;
    }

    /* =========================
       🔹 FUNGSI KEMAH RIMBUN (ID 6)
       ========================= */
    function addItemKemah() {
        const itemList = document.getElementById('item-list-kemah');
        if (!itemList) return;

        const newRow = document.createElement('div');
        newRow.className = 'item-row-kemah flex items-center gap-4 mb-4';
        newRow.innerHTML = `
            <input type="text" name="item_manual[${kemahItemCounter}][nama]" class="flex-1 rounded-md border-gray-300 shadow-sm" placeholder="Nama Item" required>
            <input type="number" name="item_manual[${kemahItemCounter}][jumlah]" class="w-24 rounded-md border-gray-300 shadow-sm" placeholder="Jumlah" value="1" min="1" required>
            <input type="number" name="item_manual[${kemahItemCounter}][harga]" class="w-24 rounded-md border-gray-300 shadow-sm item-kemah-price" placeholder="Harga" min="0" required>
            <button type="button" onclick="removeItemKemah(this)" class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
        `;

        // Pasang listener untuk total otomatis
        newRow.querySelectorAll('input[name*="[jumlah]"], input[name*="[harga]"]').forEach(inp => {
            inp.addEventListener('input', calculateTotalKemah);
        });

        itemList.appendChild(newRow);
        kemahItemCounter++;
        calculateTotalKemah();
    }

    function removeItemKemah(button) {
        button.closest('.item-row-kemah').remove();
        document.querySelectorAll('#item-list-kemah .item-row-kemah').forEach((row, index) => {
            row.querySelector('input[name*="[nama]"]').name = `item_manual[${index}][nama]`;
            row.querySelector('input[name*="[jumlah]"]').name = `item_manual[${index}][jumlah]`;
            row.querySelector('input[name*="[harga]"]').name = `item_manual[${index}][harga]`;
        });
        kemahItemCounter = document.querySelectorAll('#item-list-kemah .item-row-kemah').length;
        calculateTotalKemah();
    }

    /* =========================
       🔹 PARKIR (ID 5)
       ========================= */
    const jenisParkir = document.getElementById('jenis_kendaraan_parkir');
    const totalParkirHidden = document.getElementById('total_parkir_hidden');
    const totalParkirDisplay = document.getElementById('total_parkir_display');

    function calculateTotalParkir() {
        const jenis = jenisParkir?.value;
        let total = 0;

        const jumlah = parseFloat(document.getElementById(`jumlah_${jenis}`)?.value || 0);
        const harga  = parseFloat(document.getElementById(`harga_${jenis}`)?.value || 0);
        total = jumlah * harga;

        if (totalParkirHidden) totalParkirHidden.value = total.toFixed(0);
        if (totalParkirDisplay)
            totalParkirDisplay.value = total.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    if (jenisParkir) {
        jenisParkir.addEventListener('change', () => {
            ['motor','mobil','bis','travel'].forEach(j => {
                const field = document.getElementById(`form-${j}-fields`);
                if (field) field.classList.toggle('hidden', j !== jenisParkir.value);
            });
            calculateTotalParkir();
        });

        ['motor','mobil','bis','travel'].forEach(j => {
            document.getElementById(`jumlah_${j}`)?.addEventListener('input', calculateTotalParkir);
            document.getElementById(`harga_${j}`)?.addEventListener('input', calculateTotalParkir);
        });
    }

    /* =========================
       🔹 INISIALISASI SAAT LOAD
       ========================= */
    document.addEventListener('DOMContentLoaded', () => {
        // Aktifkan perhitungan awal untuk edit Kemah Rimbun
        if ({{ $pendapatan->id_unit }} == 6) {
            document.querySelectorAll('#item-list-kemah input[name*="[jumlah]"], #item-list-kemah input[name*="[harga]"]').forEach(input => {
                input.addEventListener('input', calculateTotalKemah);
            });
            calculateTotalKemah();
        }
    });
</script>
@endsection
