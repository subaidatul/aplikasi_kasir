<?php

namespace App\Http\Controllers;

use App\Models\Pendapatan;
use App\Models\DetailPendapatan;
use App\Models\Unit;
use App\Models\Barang;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PendapatanController extends Controller
{
    /**
     * Menampilkan daftar pendapatan dengan detail relasi.
     */
    public function index()
    {
        $pendapatans = Pendapatan::with('unit', 'detailPendapatan.barang')
            ->latest()
            ->get();

        return view('pendapatan.index', compact('pendapatans'));
    }

    /**
     * Menampilkan formulir pembuatan pendapatan baru.
     */
    public function create()
    {
        $units = Unit::all();
        $groupedBarangs = Barang::all()->groupBy('id_unit');
        $hargaDasarSewa = ['harga_paket' => 350000, 'jumlah_paket' => 50];
        $hargaDasarKemahRimbun = ['harga_item' => []];
        return view('pendapatan.create', compact('units', 'groupedBarangs', 'hargaDasarSewa', 'hargaDasarKemahRimbun'));
    }

    /**
     * Menyimpan data pendapatan baru.
     */
    public function store(Request $request)
    {
        return $this->processPendapatan($request);
    }

    /**
     * Menampilkan formulir edit pendapatan.
     */
    public function edit(Pendapatan $pendapatan)
    {
        $units = Unit::all();
        $groupedBarangs = Barang::all()->groupBy('id_unit');
        $pendapatan->load('detailPendapatan.barang');
        $hargaDasarSewa = ['harga_paket' => 350000, 'jumlah_paket' => 50];
        $hargaDasarKemahRimbun = ['harga_item' => []];

        // START: LOGIKA BARU UNTUK KEMAH RIMBUN (UNIT 6) - Mengambil data untuk form edit
        $namaPenyewaLahan = null;
        $hargaSewaLahan = null;
        
        if ($pendapatan->id_unit == 6) {
            // 1. Ambil Harga Sewa Lahan dari detailPendapatan
            $detailSewaLahan = $pendapatan->detailPendapatan->where('nama_barang_manual', 'Sewa Lahan')->first();
            if ($detailSewaLahan) {
                $hargaSewaLahan = $detailSewaLahan->total;
            }
            
            // 2. Ekstrak Nama Penyewa dari deskripsi
            if ($pendapatan->deskripsi) {
                // Mencari string "Penyewa: [Nama Penyewa],"
                $pattern = '/Penyewa:\s*([^,]*)/';
                if (preg_match($pattern, $pendapatan->deskripsi, $matches)) {
                    $namaPenyewaLahan = trim($matches[1]);
                }
            }
        }
        // END: LOGIKA BARU UNTUK KEMAH RIMBUN (UNIT 6)
        

        return view('pendapatan.edit', compact('pendapatan', 'units', 'groupedBarangs', 'hargaDasarSewa', 'hargaDasarKemahRimbun', 'namaPenyewaLahan', 'hargaSewaLahan'));
    }

    /**
     * Memperbarui data pendapatan yang sudah ada.
     */
    public function update(Request $request, Pendapatan $pendapatan)
    {
        return $this->processPendapatan($request, $pendapatan);
        {
    $pendapatan = Pendapatan::findOrFail($id);

    $data = $request->validate([
        'tanggal' => 'required|date',
        'nama_penyewa' => 'required|string',
        'tanggal_mulai' => 'nullable|date',
        'tanggal_selesai' => 'nullable|date',
        'harga_sewa_lahan' => 'nullable|numeric',
        'items' => 'nullable|array',
        'items.*.nama' => 'nullable|string',
        'items.*.jumlah' => 'nullable|numeric',
        'items.*.harga' => 'nullable|numeric'
        
    ]);

    // Update data utama pendapatan
    $pendapatan->update([
        'tanggal' => $data['tanggal'],
        'nama_penyewa' => $data['nama_penyewa'],
        'total' => $request->total_pendapatan ?? $pendapatan->total,
        'deskripsi' => $request->deskripsi,
        'detail' => json_encode([
            'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
            'harga_sewa_lahan' => $data['harga_sewa_lahan'] ?? null,
            'items' => $data['items'] ?? []
        ]),
    ]);

    // Kalau kamu punya tabel relasi item pendapatan
    if (isset($data['items'])) {
        $pendapatan->items()->delete(); // hapus item lama
        foreach ($data['items'] as $item) {
            $pendapatan->items()->create([
                'nama_item' => $item['nama'],
                'jumlah' => $item['jumlah'],
                'harga' => $item['harga']
            ]);
        }
    }

    return redirect()->route('pendapatan.index')
        ->with('success', 'Transaksi pendapatan berhasil diperbarui!');
}
    }

    /**
     * Menghapus transaksi pendapatan.
     */
    public function destroy(Pendapatan $pendapatan)
    {
        DB::beginTransaction();
        try {
            // Logika stok hanya diproses jika Unit 1
            if ($pendapatan->id_unit == 1) {
                $this->revertOldStokFromTransaction($pendapatan);
            }
            
            $pendapatan->detailPendapatan()->delete();
            $pendapatan->delete();
            
            DB::commit();
            
            $message = $pendapatan->id_unit == 1 ? 'Transaksi pendapatan berhasil dihapus dan stok dikembalikan. ✅' : 'Transaksi pendapatan berhasil dihapus. ✅';
            return redirect()->route('pendapatan.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Mengelola proses penyimpanan dan pembaruan pendapatan.
     */
    private function processPendapatan(Request $request, ?Pendapatan $pendapatan = null)
    {
        DB::beginTransaction();
        try {
            $validatedData = $this->validatePendapatan($request);
            $idUnit = $validatedData['id_unit'];

            if ($pendapatan) {
                if ($pendapatan->id_unit != $idUnit) {
                    throw new \Exception('Perubahan unit usaha tidak diizinkan. Silahkan hapus dan buat ulang transaksi.');
                }
                // Logika stok hanya diproses jika Unit 1
                if ($pendapatan->id_unit == 1) {
                    $this->revertOldStokFromTransaction($pendapatan);
                }
                $pendapatan->detailPendapatan()->delete();
            }

            $baseData = $this->getBaseData($validatedData, $idUnit, $request);
            $detailItems = $this->getDetailItems($validatedData, $idUnit);

            // Hitung dan set total akhir (khusus unit yang punya detail)
            if ($idUnit == 1 || $idUnit == 6) {
                $totalDariItems = array_sum(array_column($detailItems, 'total'));

                if ($idUnit == 6) {
                    // Unit 6: Total = Total dari semua Detail Item (Sewa Lahan + Perlengkapan)
                    $baseData['total'] = $totalDariItems;
                    
                    // Mendapatkan harga sewa lahan (sudah termasuk dalam detailItems)
                    // Cek jika 'Sewa Lahan' ada di detailItems yang baru dibuat
                    $hargaSewa = collect($detailItems)->where('nama_barang_manual', 'harga_total')->sum('total');
                    $totalTambahanItem = $totalDariItems - $hargaSewa;

                    $deskripsiTambahan = $validatedData['deskripsi'] ?? '';
                    $namaPenyewa = $validatedData['nama_penyewa'] ?? 'N/A';
                    $tglMulai = Carbon::parse($validatedData['tanggal_mulai'])->format('d/m/Y');
                    $tglSelesai = Carbon::parse($validatedData['tanggal_selesai'])->format('d/m/Y');
                    
                    // Format deskripsi agar Nama Penyewa mudah diekstrak di fungsi edit()
                    $baseData['deskripsi'] = "Kemah Rimbun:Penyewa: {$namaPenyewa}, Tgl Sewa: {$tglMulai} s/d {$tglSelesai}.";

                } else { // Unit 1: Total = Total Item Barang
                    $baseData['total'] = $totalDariItems;
                }
            }


            if ($pendapatan) {
                $pendapatan->update($baseData);
            } else {
                $pendapatan = Pendapatan::create($baseData);
            }

            if (!empty($detailItems)) {
                $pendapatan->detailPendapatan()->createMany($detailItems);
                // Update Stok hanya untuk Unit 1
                if ($idUnit == 1) {
                    $this->updateStokForDetails($detailItems, $idUnit, $baseData['tanggal']);
                }
            }

            DB::commit();
            $message = $pendapatan ? 'diperbarui' : 'disimpan';
            return redirect()->route('pendapatan.index')->with('success', "Transaksi pendapatan berhasil $message! 🎉");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Mengambil data dasar Pendapatan.
     */
    private function getBaseData(array $validatedData, int $idUnit, Request $request): array
    {
        $baseData = [
            'id_unit' => $idUnit,
            'tanggal' => Carbon::parse($validatedData['tanggal']),
            'no_pendapatan' => 'RAB-' . time(),
            'total' => null,
            'deskripsi' => $request->input('deskripsi')
        ];

        switch ($idUnit) {
            case 2:
                $jumlahPeserta = (int) $validatedData['jumlah_peserta'];
                $diskon = (float) ($validatedData['diskon'] ?? 0);
                $hargaPaket = 350000;
                $kuotaPaket = 50;

                if ($jumlahPeserta > $kuotaPaket) {
                    $hargaPerOrang = $hargaPaket / $kuotaPaket;
                    $totalHargaAwal = $jumlahPeserta * $hargaPerOrang;
                } else {
                    $totalHargaAwal = $hargaPaket;
                }

                $totalSetelahDiskon = $totalHargaAwal - ($totalHargaAwal * ($diskon / 100));

                $baseData['total'] = $totalSetelahDiskon;
                $baseData['deskripsi'] = "Sewa Tempat a.n. " . ($validatedData['nama_penyewa'] ?? '') . ", Peserta: {$jumlahPeserta}, Diskon: {$diskon}%. " . ($validatedData['deskripsi'] ?? '');
                break;
            case 3:
                $baseData['total'] = $validatedData['tiket_terjual'] * $validatedData['harga_tiket'];
                $baseData['deskripsi'] = 'Pendapatan Tiket Seluncuran. ' . ($validatedData['deskripsi'] ?? '');
                break;
            case 4:
                $jumlahSewa = $validatedData['jumlah_sewa'];
                $durasiSewa = $validatedData['durasi_sewa'];
                $tarifSewa = $validatedData['tarif_sewa'];
                $baseData['total'] = $jumlahSewa * $tarifSewa;
                $baseData['deskripsi'] = "Jumlah Sewa: {$jumlahSewa}, Durasi: {$durasiSewa}, Tarif: {$tarifSewa}. " . ($validatedData['deskripsi'] ?? '');
                break;
            case 5:
                $jenisKendaraan = $validatedData['jenis_kendaraan'];
                $totalParkir = 0;
                $deskripsiDetail = "";
                
                // Perhitungan yang lebih sederhana dan sesuai dengan validasi:
                $dataParkir = [];
                if ($jenisKendaraan === 'motor') {
                    $dataParkir = ['jumlah' => $validatedData['jumlah_motor'], 'harga' => $validatedData['harga_motor']];
                } elseif ($jenisKendaraan === 'mobil') {
                    $dataParkir = ['jumlah' => $validatedData['jumlah_mobil'], 'harga' => $validatedData['harga_mobil']];
                } elseif ($jenisKendaraan === 'bis') {
                    $dataParkir = ['jumlah' => $validatedData['jumlah_bis'], 'harga' => $validatedData['harga_bis']];
                } elseif ($jenisKendaraan === 'travel') {
                    $dataParkir = ['jumlah' => $validatedData['jumlah_travel'], 'harga' => $validatedData['harga_travel']];
                }

                if (!empty($dataParkir)) {
                    $jumlah = (int) $dataParkir['jumlah'];
                    $harga = (float) $dataParkir['harga'];
                    $totalParkir = $jumlah * $harga;
                    $deskripsiDetail = "Jenis: " . ucfirst($jenisKendaraan) . ", Jumlah: {$jumlah}, Harga: Rp" . number_format($harga, 0, ',', '.');
                }

                $baseData['total'] = $totalParkir;
                $baseData['deskripsi'] = "Pendapatan Parkir. " . $deskripsiDetail . ". " . ($validatedData['deskripsi'] ?? '');
                break;
            case 6: // Kemah Rimbun (Total dihitung di processPendapatan)
                $baseData['total'] = 0; 
                // Deskripsi akan diperbarui di processPendapatan setelah total item dihitung
                $baseData['deskripsi'] = "Pendapatan Kemah Rimbun. " . ($validatedData['deskripsi'] ?? '');
                break;
        }

        return $baseData;
    }

    /**
     * Mengambil detail item untuk unit 1 (Toko Rimbun) dan 6 (Kemah Rimbun).
     */
    private function getDetailItems(array $validatedData, int $idUnit): array
    {
        $detailItems = [];

        if ($idUnit == 1) { // Toko Rimbun (Menggunakan Barang dari DB)
            if (isset($validatedData['items'])) {
                foreach ($validatedData['items'] as $item) {
                    $barang = Barang::find($item['id_barang']);
                    if (!$barang) continue;
                    $subtotal = $barang->harga_jual * $item['jumlah'];
                    $detailItems[] = [
                        'id_barang' => $item['id_barang'],
                        'jumlah' => $item['jumlah'],
                        'harga' => $barang->harga_jual,
                        'total' => $subtotal,
                    ];
                }
            }
        } elseif ($idUnit == 6) { // Kemah Rimbun (Manual Item) - DIPERBAIKI: Tambah Sewa Lahan
            
            
            // 1. TAMBAHKAN ITEM SEWA LAHAN SECARA EKSPLISIT
            $harga_total = (float) ($validatedData['harga_total'] ?? 0);

            if ($harga_total > 0) {
                $detailItems[] = [
                    'id_barang' => null, 
                    'nama_barang_manual' => 'Sewa Lahan: Rp' . number_format($harga_total, 0, ',', '.'), // Item detail untuk sewa lahan
                    'jumlah' => 1, 
                    'harga' => $harga_total, 
                    'total' => $harga_total,
                ];
            }
            
            // 2. PROSES ITEM PERLENGKAPAN CAMPING
            $selectedItems = $validatedData['item'] ?? [];
            $jumlahItems = $validatedData['jumlah_item'] ?? [];
            $hargaItems = $validatedData['harga_item'] ?? [];

            foreach ($selectedItems as $itemName) {
                $jumlah = (int) ($jumlahItems[$itemName] ?? 0);
                $harga = (float) ($hargaItems[$itemName] ?? 0);
                
                // Pastikan item yang dicentang punya Jumlah dan Harga > 0
                if ($jumlah > 0 && $harga > 0) {
                    $subtotal = $harga * $jumlah;

                    // Mengkonversi nama item (misalnya 'jas_hujan') menjadi format judul ('Jas Hujan')
                    $namaItemFormatted = ucwords(str_replace('_', ' ', $itemName));

                    $detailItems[] = [
                        'id_barang' => null, // Item manual
                        'nama_barang_manual' => $namaItemFormatted, // Simpan nama item yang lebih rapi
                        'jumlah' => $jumlah,
                        'harga' => $harga,
                        'total' => $subtotal,
                    ];
                }
            }
        
        }

        return $detailItems;
    }

    /**
     * Memperbarui stok hanya untuk Unit 1.
     */
    private function updateStokForDetails(array $detailItems, int $idUnit, Carbon $tanggal)
    {
        if ($idUnit != 1) { // Hanya Unit 1 yang memengaruhi stok
            return;
        }

        foreach ($detailItems as $item) {
            $idBarang = $item['id_barang'] ?? null;
            if ($idBarang) {
                $this->updateStok($idBarang, $tanggal, $idUnit, $item['jumlah'], 'keluar');
            }
        }
    }

    /**
     * Memperbarui entri stok harian.
     */
    private function updateStok($idBarang, $tanggal, $idUnit, $jumlah, $tipe)
    {
        $stokEntry = Stok::firstOrNew([
            'id_barang' => $idBarang,
            'tanggal' => Carbon::parse($tanggal)->toDateString(),
            'id_unit' => $idUnit,
        ]);
        $lastStokBefore = Stok::where('id_barang', $idBarang)->where('tanggal', '<', Carbon::parse($tanggal)->toDateString())->latest('tanggal')->latest('created_at')->first();
        $initialStokToday = $lastStokBefore ? $lastStokBefore->sisa_stok : 0;
        $currentStokMasuk = $stokEntry->stok_masuk ?? 0;
        $currentStokKeluar = $stokEntry->stok_keluar ?? 0;

        if ($tipe == 'masuk') {
            $stokEntry->stok_masuk = $currentStokMasuk + $jumlah;
        } else {
            $newStokKeluar = $currentStokKeluar + $jumlah;
            if ($initialStokToday + $currentStokMasuk - $newStokKeluar < 0) {
                throw new \Exception('Stok barang tidak mencukupi.');
            }
            $stokEntry->stok_keluar = $newStokKeluar;
        }
        $stokEntry->sisa_stok = $initialStokToday + ($stokEntry->stok_masuk ?? 0) - ($stokEntry->stok_keluar ?? 0);
        $stokEntry->keterangan = 'Penyesuaian Stok Otomatis';
        $stokEntry->save();
        $this->recalculateFutureStok($idBarang, $tanggal);
    }

    /**
     * Mengembalikan stok lama hanya untuk Unit 1.
     */
    private function revertOldStokFromTransaction(Pendapatan $pendapatan)
    {
        if ($pendapatan->id_unit != 1) {
            return;
        }
        if ($pendapatan->detailPendapatan->isNotEmpty()) {
            foreach ($pendapatan->detailPendapatan as $detail) {
                if ($detail->id_barang) {
                    $stokEntry = Stok::where('id_barang', $detail->id_barang)->where('tanggal', $pendapatan->tanggal)->first();
                    if ($stokEntry) {
                        $stokEntry->stok_keluar -= $detail->jumlah;
                        $stokEntry->sisa_stok += $detail->jumlah;
                        $stokEntry->save();
                        $this->recalculateFutureStok($stokEntry->id_barang, $stokEntry->tanggal);
                    }
                }
            }
        }
    }

    /**
     * Menghitung ulang stok harian setelah tanggal tertentu.
     */
    private function recalculateFutureStok($idBarang, $startDate)
    {
        $stokEntries = Stok::where('id_barang', $idBarang)->where('tanggal', '>', $startDate)->orderBy('tanggal')->orderBy('created_at')->get();
        $lastStokBefore = Stok::where('id_barang', $idBarang)->where('tanggal', $startDate)->latest('created_at')->first();
        $currentSisaStok = $lastStokBefore ? $lastStokBefore->sisa_stok : 0;
        
        foreach ($stokEntries as $entry) {
            $newSisaStok = $currentSisaStok + $entry->stok_masuk - $entry->stok_keluar;
            if ($entry->sisa_stok === $newSisaStok) {
                $currentSisaStok = $newSisaStok;
                continue;
            }
            $entry->sisa_stok = $newSisaStok;
            $entry->save();
            $currentSisaStok = $newSisaStok;
        }
    }

    /**
     * Validasi data permintaan berdasarkan unit usaha.
     */
    protected function validatePendapatan(Request $request)
    {
        $baseRules = [
            'id_unit' => 'required|exists:unit,id_unit',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string'
        ];

        switch ($request->id_unit) {
            case 1: 
                $rules = array_merge($baseRules, [
                    'items' => 'required|array|min:1',
                    'items.*.id_barang' => 'required|exists:barang,id_barang',
                    'items.*.jumlah' => 'required|integer|min:1'
                ]);
                break;
            case 2:
                $rules = array_merge($baseRules, [
                    'nama_penyewa' => 'required|string',
                    'jumlah_peserta' => 'required|integer|min:1',
                    'diskon' => 'nullable|numeric|min:0|max:100'
                ]);
                break;
            case 3:
                $rules = array_merge($baseRules, [
                    'tiket_terjual' => 'required|integer|min:1',
                    'harga_tiket' => 'required|numeric|min:0'
                ]);
                break;
            case 4:
                $rules = array_merge($baseRules, [
                    'jumlah_sewa' => 'required|integer|min:1',
                    'durasi_sewa' => 'required|integer|min:1',
                    'tarif_sewa' => 'required|numeric|min:0'
                ]);
                break;
            case 5:
                $rules = array_merge($baseRules, [
                    'jenis_kendaraan' => ['required', Rule::in(['motor', 'mobil', 'bis', 'travel'])],
                ]);

                $jenisKendaraan = $request->input('jenis_kendaraan');
                $rules = array_merge($rules, [
                    "jumlah_{$jenisKendaraan}" => 'required|integer|min:1',
                    "harga_{$jenisKendaraan}" => 'required|numeric|min:0',
                ]);
                break;
            case 6: // Kemah Rimbun
                $rules = array_merge($baseRules, [
                    'nama_penyewa' => 'required|string|max:255',
                    'tanggal_mulai' => 'required|date',
                    'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                    'harga_total' => 'required|numeric|min:0', // Harga Sewa Lahan

                    // Menangkap input dari HTML yang Anda berikan (item yang dicentang)
                    'item' => 'nullable|array',
                    'item.*' => 'string', 
                    
                    // --- VALIDASI PERLENGKAPAN TIDUR & TEMPAT ---
                    'jumlah_item.tenda_2_orang' => 'nullable|integer|min:0',
                    'harga_item.tenda_2_orang' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.tenda_4_orang' => 'nullable|integer|min:0',
                    'harga_item.tenda_4_orang' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.tenda_6_orang' => 'nullable|integer|min:0',
                    'harga_item.tenda_6_orang' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.matras' => 'nullable|integer|min:0',
                    'harga_item.matras' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.sleeping_bag' => 'nullable|integer|min:0',
                    'harga_item.sleeping_bag' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.hammock_parachute' => 'nullable|integer|min:0',
                    'harga_item.hammock_parachute' => 'nullable|numeric|min:0',

                    'jumlah_item.flysheet_tarp' => 'nullable|integer|min:0',
                    'harga_item.flysheet_tarp' => 'nullable|numeric|min:0',

                    'jumlah_item.bantal_angin' => 'nullable|integer|min:0',
                    'harga_item.bantal_angin' => 'nullable|numeric|min:0',
                    
                    // --- VALIDASI PERALATAN MASAK & MAKAN ---
                    'jumlah_item.kompor_portable' => 'nullable|integer|min:0',
                    'harga_item.kompor_portable' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.nesting_set' => 'nullable|integer|min:0',
                    'harga_item.nesting_set' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.peralatan_makan' => 'nullable|integer|min:0',
                    'harga_item.peralatan_makan' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.cooler_box' => 'nullable|integer|min:0',
                    'harga_item.cooler_box' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.barbeque_grill' => 'nullable|integer|min:0',
                    'harga_item.barbeque_grill' => 'nullable|numeric|min:0',
                    
                    // --- VALIDASI API UNGGUN & HIBURAN ---
                    'jumlah_item.paket_api_unggun' => 'nullable|integer|min:0',
                    'harga_item.paket_api_unggun' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.kursi_meja_lipat' => 'nullable|integer|min:0',
                    'harga_item.kursi_meja_lipat' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.guitar_ukulele' => 'nullable|integer|min:0',
                    'harga_item.guitar_ukulele' => 'nullable|numeric|min:0',
                    
                    // --- VALIDASI PENERANGAN & KEAMANAN ---
                    'jumlah_item.lentera_camping' => 'nullable|integer|min:0',
                    'harga_item.lentera_camping' => 'nullable|numeric|min:0',
                    
                    'jumlah_item.headlamp_senter' => 'nullable|integer|min:0',
                    'harga_item.headlamp_senter' => 'nullable|numeric|min:0',

                    // Validasi untuk Jumlah dan Harga Powerbank
                    'jumlah_item.powerbank' => 'nullable|integer|min:0',
                    'harga_item.powerbank' => 'nullable|numeric|min:0',
                    
                    // Validasi untuk Jumlah dan Harga Jas Hujan
                    'jumlah_item.jas_hujan' => 'nullable|integer|min:0',
                    'harga_item.jas_hujan' => 'nullable|numeric|min:0',
                    
                    // Tambahkan validasi untuk item manual lainnya jika ada
                ]);
                break;
            default:
                $rules = $baseRules;
        }

        return $request->validate($rules);
    }

    public function cetakStruk($id)
    {
        try {
            $data = Pendapatan::with('unit', 'detailPendapatan.barang')->find($id);
            if (!$data) {
                abort(404, 'Transaksi pendapatan tidak ditemukan.');
            }
            $jenis = 'pendapatan';
            return view('struk.show', compact('data', 'jenis'));
        } catch (ModelNotFoundException $e) {
            abort(404, 'Transaksi pendapatan tidak ditemukan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mencetak struk: ' . $e->getMessage());
        }
    }
}