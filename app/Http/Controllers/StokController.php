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
            $stokEntry = Stok::firstOrNew([
                'id_barang' => $validatedData['id_barang'],
                'tanggal' => $validatedData['tanggal'],
                'id_unit' => $validatedData['id_unit'],
            ]);

            $lastStokBefore = Stok::where('id_barang', $validatedData['id_barang'])
                ->where('tanggal', '<', $validatedData['tanggal'])
                ->latest('tanggal')
                ->latest('created_at')
                ->first();

            $initialStokToday = $lastStokBefore ? $lastStokBefore->sisa_stok : 0;
            $stokEntry->stok_masuk += $validatedData['jumlah'];

            $newSisaStok = $initialStokToday + $stokEntry->stok_masuk - $stokEntry->stok_keluar;
            $stokEntry->sisa_stok = $newSisaStok;
            $stokEntry->keterangan = $validatedData['keterangan'] ?? 'Penambahan Stok Manual';
            $stokEntry->save();

            $this->recalculateFutureStok($stokEntry->id_barang, $stokEntry->tanggal);

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
            $oldIdBarang = $stok->id_barang;
            $oldTanggal = $stok->tanggal;
            $oldStokMasuk = $stok->stok_masuk;

            // Jika ada perubahan pada barang atau tanggal, kita hapus entri lama
            // dan buat entri baru untuk memicu perhitungan ulang penuh.
            if ($oldIdBarang != $validatedData['id_barang'] || $oldTanggal != $validatedData['tanggal']) {
                $stok->delete();
                $this->recalculateFutureStok($oldIdBarang, $oldTanggal); // Hitung ulang stok lama

                // Proses data baru seolah-olah dari form store()
                $this->store($request);
                DB::commit();
                return redirect()->route('admin.stok.index')->with('success', 'Data stok berhasil diperbarui! ✅');
            }

            // Jika tidak ada perubahan pada id_barang atau tanggal,
            // cukup update entri yang ada dan panggil perhitungan ulang
            $stok->stok_masuk = $validatedData['jumlah'];
            $stok->keterangan = $validatedData['keterangan'] ?? 'Perubahan Stok Manual';
            $stok->save();

            $this->recalculateFutureStok($stok->id_barang, $stok->tanggal);

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
}