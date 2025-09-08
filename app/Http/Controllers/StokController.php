<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Stok;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StokController extends Controller
{
    public function index()
    {
        $stoks = Stok::with(['barang', 'unit'])->latest()->paginate(15);
        return view('stok.index', compact('stoks'));
    }

    public function create()
    {
        $barangs = Barang::all();
        $units = Unit::all();
        return view('stok.create', compact('barangs', 'units'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'id_unit' => 'required|exists:unit,id_unit',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Temukan entri stok terakhir sebelum tanggal yang diinput
            $lastStokBefore = Stok::where('id_barang', $validatedData['id_barang'])
                ->where('tanggal', '<', $validatedData['tanggal'])
                ->latest('tanggal')
                ->latest('created_at')
                ->first();

            $currentSisaStok = $lastStokBefore ? $lastStokBefore->sisa_stok : 0;
            $newSisaStok = $currentSisaStok + $validatedData['jumlah'];

            // Buat entri stok baru
            Stok::create([
                'id_barang' => $validatedData['id_barang'],
                'id_unit' => $validatedData['id_unit'],
                'tanggal' => $validatedData['tanggal'],
                'keterangan' => $validatedData['keterangan'] ?? 'Penambahan Stok Manual',
                'stok_masuk' => $validatedData['jumlah'],
                'stok_keluar' => 0,
                'sisa_stok' => $newSisaStok,
            ]);

            // Hitung ulang stok untuk hari-hari setelah tanggal ini
            $this->recalculateFutureStok($validatedData['id_barang'], $validatedData['tanggal']);

            DB::commit();
            return redirect()->route('admin.stok.index')->with('success', 'Stok berhasil ditambahkan! ✅');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan stok: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Stok $stok)
    {
        $barangs = Barang::all();
        $units = Unit::all();
        return view('stok.edit', compact('stok', 'barangs', 'units'));
    }

    public function update(Request $request, Stok $stok)
    {
        $validatedData = $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'id_unit' => 'required|exists:unit,id_unit',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Simpan data lama untuk perhitungan ulang
            $idBarang = $stok->id_barang;
            $tanggal = $stok->tanggal;

            // **PERBAIKAN KRITIS:**
            // Update entri stok yang ada dengan data baru.
            // Asumsi form edit hanya untuk operasi penambahan (stok masuk),
            // maka stok_keluar direset ke 0.
            $stok->update([
                'id_barang' => $validatedData['id_barang'],
                'id_unit' => $validatedData['id_unit'],
                'tanggal' => $validatedData['tanggal'],
                'keterangan' => $validatedData['keterangan'] ?? 'Perubahan Stok Manual',
                'stok_masuk' => $validatedData['jumlah'],
                'stok_keluar' => 0,
            ]);

            // Panggil kembali fungsi untuk menghitung ulang stok ke depan
            $this->recalculateFutureStok($idBarang, $tanggal);

            DB::commit();
            return redirect()->route('admin.stok.index')->with('success', 'Data stok berhasil diperbarui! ✅');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui stok: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Stok $stok)
    {
        DB::beginTransaction();
        try {
            $idBarang = $stok->id_barang;
            $tanggal = $stok->tanggal;

            $stok->delete();

            // Panggil kembali fungsi untuk menghitung ulang stok ke depan
            $this->recalculateFutureStok($idBarang, $tanggal);

            DB::commit();
            return redirect()->route('admin.stok.index')->with('success', 'Catatan stok berhasil dihapus. ✅');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus catatan stok: ' . $e->getMessage());
        }
    }

    private function recalculateFutureStok($idBarang, $startDate)
    {
        // Ambil semua entri stok untuk barang ini dari tanggal yang ditentukan hingga yang paling baru
        $stokEntries = Stok::where('id_barang', $idBarang)
            ->where('tanggal', '>=', $startDate)
            ->orderBy('tanggal')
            ->orderBy('created_at')
            ->get();

        // Cari sisa stok terakhir dari hari-hari sebelum tanggal yang ditentukan
        $lastStokBefore = Stok::where('id_barang', $idBarang)
            ->where('tanggal', '<', $startDate)
            ->latest('tanggal')
            ->latest('created_at')
            ->first();

        // Tentukan sisa stok awal untuk perhitungan
        $currentSisaStok = $lastStokBefore ? $lastStokBefore->sisa_stok : 0;

        foreach ($stokEntries as $entry) {
            // Hitung sisa stok baru berdasarkan sisa stok sebelumnya dan stok masuk/keluar entri ini
            $newSisaStok = $currentSisaStok + $entry->stok_masuk - $entry->stok_keluar;

            // Jika sisa stok tidak berubah, lewati
            if ($entry->sisa_stok === $newSisaStok) {
                $currentSisaStok = $newSisaStok;
                continue;
            }

            $entry->sisa_stok = $newSisaStok;
            $entry->save();

            // Perbarui sisa stok untuk iterasi berikutnya
            $currentSisaStok = $newSisaStok;
        }
    }
}