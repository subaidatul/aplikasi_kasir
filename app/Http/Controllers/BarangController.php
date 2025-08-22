<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Unit;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar barang, hanya barang pendapatan untuk unit Cafe.
     */
    public function index()
    {
        $cafeUnitId = config('app.cafe_unit_id', 1);

        $barangPendapatanCafe = Barang::with('unit')
            ->where('id_unit', $cafeUnitId)
            ->where('harga_jual', '>', 0)
            ->latest() // Menambahkan orderBy agar data terbaru muncul di atas
            ->get();

        return view('barang.index', compact('barangPendapatanCafe'));
    }

    /**
     * Menampilkan form untuk menambah barang baru.
     */
    public function create(Request $request)
    {
        $type = $request->query('type', 'pendapatan');
        return view('barang.create', compact('type'));
    }

    /**
     * Menyimpan barang baru.
     */
    public function store(Request $request)
    {
        return $this->processBarang($request);
    }

    /**
     * Menampilkan form untuk mengedit barang.
     */
    public function edit(Barang $barang)
    {
        $units = Unit::all();
        return view('barang.edit', compact('barang', 'units'));
    }

    /**
     * Memperbarui data barang.
     */
    public function update(Request $request, Barang $barang)
    {
        return $this->processBarang($request, $barang);
    }

    /**
     * Menghapus barang.
     */
    public function destroy(Barang $barang)
    {
        // Periksa apakah ada relasi yang terkait sebelum menghapus
        if ($barang->detailPendapatan()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus barang karena sudah memiliki transaksi pendapatan.');
        }

        DB::beginTransaction();
        try {
            // Hapus catatan stok terkait barang
            $barang->stok()->delete();
            $barang->delete();

            DB::commit();
            return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }

    /**
     * Metode privat untuk memproses penyimpanan atau pembaruan barang.
     *
     * @param Request $request
     * @param Barang|null $barang
     * @return \Illuminate\Http\RedirectResponse
     */
    private function processBarang(Request $request, ?Barang $barang = null)
    {
        $rules = [
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => ['required', 'string', 'max:50', Rule::unique('barang', 'kode_barang')->ignore($barang->id_barang ?? null, 'id_barang')],
            'kategori_produk' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'status' => 'required|string|in:aktif,nonaktif',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['id_unit'] = config('app.cafe_unit_id', 1);

        DB::beginTransaction();
        try {
            // Hitung selisih stok jika sedang update, jika tidak, selisihnya adalah stok awal
            $oldStok = $barang ? $barang->stok : 0;
            $diffStok = $validatedData['stok'] - $oldStok;

            if ($barang) {
                $barang->update($validatedData);
            } else {
                $barang = Barang::create($validatedData);
            }

            // Catat perubahan stok hanya jika ada selisih
            if ($diffStok != 0) {
                Stok::create([
                    'id_barang' => $barang->id_barang,
                    'id_unit' => $barang->id_unit,
                    'no_transaksi' => 'STK-' . time(),
                    'tanggal' => now(),
                    'keterangan' => $diffStok > 0 ? 'Penambahan Stok' : 'Pengurangan Stok',
                    'stok_masuk' => max(0, $diffStok),
                    'stok_keluar' => abs(min(0, $diffStok)),
                    'sisa_stok' => $barang->stok,
                ]);
            }

            DB::commit();

            $message = $barang ? 'diperbarui' : 'ditambahkan';
            return redirect()->route('barang.index')->with('success', "Barang berhasil $message!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses barang: ' . $e->getMessage())->withInput();
        }
    }
}