<?php

namespace App\Http\Controllers;

use App\Models\Pendapatan;
use App\Models\DetailPendapatan;
use App\Models\Unit;
use App\Models\Barang;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendapatanController extends Controller
{
    public function index()
    {
        $pendapatans = Pendapatan::with('unit', 'detailPendapatan.barang')
                                 ->latest()
                                 ->get();
        return view('pendapatan.index', compact('pendapatans'));
    }
    
    public function create()
    {
        $units = Unit::all();
        $groupedBarangs = Barang::all()->groupBy('id_unit');
        return view('pendapatan.create', compact('units', 'groupedBarangs'));
    }

    public function store(Request $request)
    {
        return $this->processPendapatan($request);
    }

    public function edit(Pendapatan $pendapatan)
    {
        $units = Unit::all();
        // Ambil barang yang termasuk dalam unit "Cafe/Wifi/Parkir" (id_unit = 1)
        $barangPendapatanCafe = Barang::where('id_unit', 1)->get(); 
        $groupedBarangs = Barang::all()->groupBy('id_unit');
        $pendapatan->load('detailPendapatan.barang'); 

        // Menggunakan id_unit 1 secara eksplisit untuk barang
        return view('pendapatan.edit', compact('pendapatan', 'units', 'groupedBarangs', 'barangPendapatanCafe'));
    }

    public function update(Request $request, Pendapatan $pendapatan)
    {
        return $this->processPendapatan($request, $pendapatan);
    }

    public function destroy(Pendapatan $pendapatan)
    {
        DB::beginTransaction();

        try {
            // Jika transaksi terkait dengan barang yang memiliki stok
            if ($pendapatan->detailPendapatan->isNotEmpty()) {
                foreach ($pendapatan->detailPendapatan as $detail) {
                    $barang = Barang::find($detail->id_barang);
                    if ($barang) {
                        $barang->stok += $detail->jumlah;
                        $barang->save();

                        // Catat pengembalian stok ke tabel stok
                        Stok::create([
                            'id_barang' => $barang->id_barang,
                            'id_unit' => $pendapatan->id_unit,
                            'no_transaksi' => $pendapatan->no_pendapatan,
                            'tanggal' => now(),
                            'keterangan' => 'Pembatalan Penjualan',
                            'stok_masuk' => $detail->jumlah,
                            'stok_keluar' => 0,
                            'sisa_stok' => $barang->stok,
                        ]);
                    }
                }
            }

            // Hapus semua detail dan pendapatan
            $pendapatan->detailPendapatan()->delete();
            $pendapatan->delete();

            DB::commit();
            return redirect()->route('pendapatan.index')->with('success', 'Transaksi pendapatan berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Metode privat untuk memproses penyimpanan atau pembaruan pendapatan.
     *
     * @param Request $request
     * @param Pendapatan|null $pendapatan
     * @return \Illuminate\Http\RedirectResponse
     */
    private function processPendapatan(Request $request, ?Pendapatan $pendapatan = null)
    {
        DB::beginTransaction();
        try {
            $validatedData = $this->validatePendapatan($request);
            $idUnit = $validatedData['id_unit'];
            $noPendapatan = $pendapatan ? $pendapatan->no_pendapatan : 'TRX-' . time();
            
            // Logika untuk mengelola stok saat update
            if ($pendapatan) {
                // Hapus detail lama dan kembalikan stok
                $this->revertOldStok($pendapatan);
                $pendapatan->detailPendapatan()->delete();
            }

            // --- PERBAIKAN LOGIKA DI SINI ---
            // Buat atau perbarui record Pendapatan terlebih dahulu
            $baseData = [
                'id_unit' => $idUnit,
                'tanggal' => $validatedData['tanggal'],
                'no_pendapatan' => $noPendapatan,
            ];

            // Tentukan total dan deskripsi berdasarkan unit
            switch ($idUnit) {
                case 1: // Cafe/Wifi/Parkir (Barang)
                    $totalPendapatan = 0;
                    foreach ($validatedData['items'] as $item) {
                        $barang = Barang::find($item['id_barang']);
                        $totalPendapatan += $barang->harga_jual * $item['jumlah'];
                    }
                    $baseData['total'] = $totalPendapatan;
                    $baseData['deskripsi'] = $validatedData['deskripsi'] ?? null;
                    break;
                case 2: // Sewa Tempat
                    $baseData['total'] = $validatedData['harga_akhir'];
                    $baseData['deskripsi'] = 'Pendapatan Sewa Tempat atas nama ' . ($validatedData['nama_penyewa'] ?? '') . '. ' . ($validatedData['deskripsi'] ?? '');
                    break;
                case 3: // Seluncuran
                    $baseData['total'] = $validatedData['tiket_terjual'] * $validatedData['harga_tiket'];
                    $baseData['deskripsi'] = 'Pendapatan Tiket Seluncuran. ' . ($validatedData['deskripsi'] ?? '');
                    break;
                case 4: // ATV
                    $baseData['total'] = $validatedData['jumlah_sewa'] * 100000;
                    $baseData['deskripsi'] = 'Pendapatan Sewa ATV. ' . ($validatedData['deskripsi'] ?? '');
                    break;
                default:
                    // Logika untuk unit lain jika ada
                    $baseData['total'] = 0;
                    $baseData['deskripsi'] = $validatedData['deskripsi'] ?? null;
                    break;
            }

            if ($pendapatan) {
                $pendapatan->update($baseData);
            } else {
                $pendapatan = Pendapatan::create($baseData);
            }

            // Proses detail hanya jika unit usaha menjual barang (contoh: id_unit 1)
            if ($idUnit == 1) { 
                foreach ($validatedData['items'] as $item) {
                    $barang = Barang::find($item['id_barang']);
                    
                    if (!$barang) {
                        throw new \Exception('Barang tidak ditemukan.');
                    }
    
                    if ($barang->stok < $item['jumlah']) {
                        throw new \Exception('Stok barang ' . $barang->nama_barang . ' tidak mencukupi.');
                    }

                    // Tambahkan detail pendapatan
                    DetailPendapatan::create([
                        'id_pendapatan' => $pendapatan->id_pendapatan,
                        'id_barang' => $item['id_barang'],
                        'jumlah' => $item['jumlah'],
                        'harga' => $barang->harga_jual, // Tambahkan harga di sini
                        'total' => $barang->harga_jual * $item['jumlah'],
                    ]);

                    // Kurangi stok barang dan catat ke tabel stok
                    $barang->stok -= $item['jumlah'];
                    $barang->save();
                    Stok::create([
                        'id_barang' => $barang->id_barang,
                        'id_unit' => $idUnit,
                        'no_transaksi' => $noPendapatan,
                        'tanggal' => $validatedData['tanggal'],
                        'keterangan' => 'Penjualan',
                        'stok_masuk' => 0,
                        'stok_keluar' => $item['jumlah'],
                        'sisa_stok' => $barang->stok,
                    ]);
                }
            }

            DB::commit();
            $message = $pendapatan ? 'diperbarui' : 'disimpan';
            return redirect()->route('pendapatan.index')->with('success', "Transaksi pendapatan berhasil $message!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }
    
    // --- Metode lain tetap sama ---
    private function revertOldStok(Pendapatan $pendapatan)
    {
        foreach ($pendapatan->detailPendapatan as $detail) {
            $barang = Barang::find($detail->id_barang);
            if ($barang) {
                $barang->stok += $detail->jumlah;
                $barang->save();
            }
        }
        Stok::where('no_transaksi', $pendapatan->no_pendapatan)->delete();
    }
    
    protected function validatePendapatan(Request $request)
    {
        $baseRules = [
            'id_unit' => 'required|exists:unit,id_unit',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string',
        ];

        // Tambahkan validasi sesuai unit yang dipilih
        switch ($request->id_unit) {
            case 1: // Cafe/Wifi/Parkir
                $rules = array_merge($baseRules, [
                    'items' => 'required|array',
                    'items.*.id_barang' => 'required|exists:barang,id_barang',
                    'items.*.jumlah' => 'required|integer|min:1',
                ]);
                break;
            case 2: // Sewa Tempat
                $rules = array_merge($baseRules, [
                    'nama_penyewa' => 'required|string',
                    'harga_akhir' => 'required|numeric|min:0',
                ]);
                break;
            case 3: // Seluncuran
                $rules = array_merge($baseRules, [
                    'tiket_terjual' => 'required|integer|min:1',
                    'harga_tiket' => 'required|numeric|min:0',
                ]);
                break;
            case 4: // ATV
                $rules = array_merge($baseRules, [
                    'jumlah_sewa' => 'required|integer|min:1',
                ]);
                break;
            default:
                $rules = $baseRules;
                break;
        }

        return $request->validate($rules);
    }
}