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
        $barangPendapatanCafe = Barang::where('id_unit', 1)->get();
        $groupedBarangs = Barang::all()->groupBy('id_unit');
        $pendapatan->load('detailPendapatan.barang');

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
            if ($pendapatan->detailPendapatan->isNotEmpty()) {
                foreach ($pendapatan->detailPendapatan as $detail) {
                    $stokEntry = Stok::where('id_barang', $detail->id_barang)
                        ->where('tanggal', $pendapatan->tanggal)
                        ->first();

                    if ($stokEntry) {
                        $stokEntry->stok_keluar -= $detail->jumlah;
                        $stokEntry->sisa_stok = $stokEntry->sisa_stok + $detail->jumlah;
                        $stokEntry->save();

                        $this->recalculateFutureStok($stokEntry->id_barang, $stokEntry->tanggal);
                    }
                }
            }

            $pendapatan->detailPendapatan()->delete();
            $pendapatan->delete();

            DB::commit();
            return redirect()->route('pendapatan.index')->with('success', 'Transaksi pendapatan berhasil dihapus dan stok dikembalikan. ✅');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    private function processPendapatan(Request $request, ?Pendapatan $pendapatan = null)
    {
        DB::beginTransaction();
        try {
            $validatedData = $this->validatePendapatan($request);
            $idUnit = $validatedData['id_unit'];
            $noPendapatan = $pendapatan ? $pendapatan->no_pendapatan : 'TRX-' . time();

            if ($pendapatan) {
                $this->revertOldStokFromTransaction($pendapatan);
                $pendapatan->detailPendapatan()->delete();
            }

            if ($idUnit == 1) {
                foreach ($validatedData['items'] as $item) {
                    $barang = Barang::find($item['id_barang']);
                    if (!$barang) {
                        throw new \Exception('Barang tidak ditemukan.');
                    }

                    $stokEntry = Stok::firstOrNew([
                        'id_barang' => $item['id_barang'],
                        'tanggal' => $validatedData['tanggal'],
                        'id_unit' => $idUnit,
                    ]);

                    $currentSisaStok = $stokEntry->exists ? $stokEntry->sisa_stok : (Stok::where('id_barang', $item['id_barang'])
                        ->where('tanggal', '<', $validatedData['tanggal'])
                        ->latest('tanggal')
                        ->latest('created_at')
                        ->first()
                        ->sisa_stok ?? 0);

                    $stokEntry->stok_keluar += $item['jumlah'];
                    $newSisaStok = $currentSisaStok + $stokEntry->stok_masuk - $stokEntry->stok_keluar;

                    if ($newSisaStok < 0) {
                        throw new \Exception('Stok barang ' . $barang->nama_barang . ' tidak mencukupi. Sisa stok: ' . ($currentSisaStok + $stokEntry->stok_masuk - ($stokEntry->stok_keluar - $item['jumlah'])) . ', keluar: ' . $item['jumlah']);
                    }

                    $stokEntry->sisa_stok = $newSisaStok;
                    $stokEntry->keterangan = 'Penjualan dan Penambahan Stok Manual';
                    $stokEntry->save();
                    $this->recalculateFutureStok($stokEntry->id_barang, $stokEntry->tanggal);
                }
            }

            $baseData = [
                'id_unit' => $idUnit,
                'tanggal' => $validatedData['tanggal'],
                'no_pendapatan' => $noPendapatan,
            ];

            switch ($idUnit) {
                case 1:
                    $totalPendapatan = 0;
                    foreach ($validatedData['items'] as $item) {
                        $barang = Barang::find($item['id_barang']);
                        $totalPendapatan += $barang->harga_jual * $item['jumlah'];
                    }
                    $baseData['total'] = $totalPendapatan;
                    $baseData['deskripsi'] = $validatedData['deskripsi'] ?? null;
                    break;
                case 2:
                    $baseData['total'] = $validatedData['harga_akhir'];
                    $baseData['deskripsi'] = 'Pendapatan Sewa Tempat atas nama ' . ($validatedData['nama_penyewa'] ?? '') . '. ' . ($validatedData['deskripsi'] ?? '');
                    break;
                case 3:
                    $baseData['total'] = $validatedData['tiket_terjual'] * $validatedData['harga_tiket'];
                    $baseData['deskripsi'] = 'Pendapatan Tiket Seluncuran. ' . ($validatedData['deskripsi'] ?? '');
                    break;
                case 4:
                    $jumlahSewa = $validatedData['jumlah_sewa'];
                    $durasiSewa = $validatedData['durasi_sewa'];
                    $tarifSewa = $validatedData['tarif_sewa'];

                    $baseData['total'] = $jumlahSewa * $durasiSewa * $tarifSewa;

                    // Simpan data mentah di deskripsi dengan format khusus
                    $deskripsi = "Jumlah Sewa: {$jumlahSewa}, Durasi: {$durasiSewa}, Tarif: {$tarifSewa}.";
                    $baseData['deskripsi'] = $deskripsi . ($validatedData['deskripsi'] ?? '');
                    break;
            }

            if ($pendapatan) {
                $pendapatan->update($baseData);
            } else {
                $pendapatan = Pendapatan::create($baseData);
            }

            if ($idUnit == 1) {
                foreach ($validatedData['items'] as $item) {
                    $barang = Barang::find($item['id_barang']);
                    DetailPendapatan::create([
                        'id_pendapatan' => $pendapatan->id_pendapatan,
                        'id_barang' => $item['id_barang'],
                        'jumlah' => $item['jumlah'],
                        'harga' => $barang->harga_jual,
                        'total' => $barang->harga_jual * $item['jumlah'],
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

    private function revertOldStokFromTransaction(Pendapatan $pendapatan)
    {
        if ($pendapatan->detailPendapatan->isNotEmpty()) {
            foreach ($pendapatan->detailPendapatan as $detail) {
                $stokEntry = Stok::where('id_barang', $detail->id_barang)
                    ->where('tanggal', $pendapatan->tanggal)
                    ->first();

                if ($stokEntry) {
                    $stokEntry->stok_keluar -= $detail->jumlah;
                    $stokEntry->sisa_stok += $detail->jumlah;
                    $stokEntry->save();

                    $this->recalculateFutureStok($stokEntry->id_barang, $stokEntry->tanggal);
                }
            }
        }
    }

    private function recalculateFutureStok($idBarang, $startDate)
    {
        $stokEntries = Stok::where('id_barang', $idBarang)
            ->where('tanggal', '>=', $startDate)
            ->orderBy('tanggal')
            ->orderBy('created_at')
            ->get();

        $lastStokBefore = Stok::where('id_barang', $idBarang)
            ->where('tanggal', '<', $startDate)
            ->latest('tanggal')
            ->latest('created_at')
            ->first();

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

    protected function validatePendapatan(Request $request)
    {
        $baseRules = [
            'id_unit' => 'required|exists:unit,id_unit',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string',
        ];

        switch ($request->id_unit) {
            case 1:
                $rules = array_merge($baseRules, [
                    'items' => 'required|array',
                    'items.*.id_barang' => 'required|exists:barang,id_barang',
                    'items.*.jumlah' => 'required|integer|min:1',
                ]);
                break;
            case 2:
                $rules = array_merge($baseRules, [
                    'nama_penyewa' => 'required|string',
                    'harga_akhir' => 'required|numeric|min:0',
                ]);
                break;
            case 3:
                $rules = array_merge($baseRules, [
                    'tiket_terjual' => 'required|integer|min:1',
                    'harga_tiket' => 'required|numeric|min:0',
                ]);
                break;
            case 4:
                // PERBAIKAN DI SINI
                $rules = array_merge($baseRules, [
                    'jumlah_sewa' => 'required|integer|min:1',
                    'durasi_sewa' => 'required|integer|min:1',
                    'tarif_sewa' => 'required|numeric|min:0',
                ]);
                break;
            default:
                $rules = $baseRules;
                break;
        }

        return $request->validate($rules);
    }

    public function cetakStruk($id)
    {
        try {
            // Mengambil data pendapatan berdasarkan ID
            $data = Pendapatan::with('unit', 'detailPendapatan.barang')->find($id);

            // Jika data tidak ditemukan, tampilkan halaman 404
            if (!$data) {
                abort(404, 'Transaksi pendapatan tidak ditemukan.');
            }

            // Tambahkan variabel 'jenis' di sini
            $jenis = 'pendapatan';

            // Kirim 'data' DAN 'jenis' ke view
            return view('struk.show', compact('data', 'jenis'));
        } catch (ModelNotFoundException $e) {
            // Tangkap exception jika ID tidak ditemukan dan kembalikan 404
            abort(404, 'Transaksi pendapatan tidak ditemukan.');
        } catch (\Exception $e) {
            // Tangkap exception lainnya
            return back()->with('error', 'Gagal mencetak struk: ' . $e->getMessage());
        }
    }
}
