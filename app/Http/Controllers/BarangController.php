<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            ->latest()
            ->get();

        return view('barang.index', compact('barangPendapatanCafe'));
    }

    /**
     * Menampilkan form untuk menambah barang baru.
     */
    public function create()
    {
        $units = Unit::all();
        return view('barang.create', compact('units'));
    }

    /**
     * Menyimpan barang baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => ['required', 'string', 'max:50', Rule::unique('barang', 'kode_barang')],
            'kategori_produk' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'status' => 'required|string|in:aktif,nonaktif',
            'id_unit' => 'required|exists:unit,id_unit',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $barang = new Barang;
            $barang->fill($validatedData);

            // Proses penyimpanan gambar
            if ($request->hasFile('gambar')) {
                // Simpan gambar ke storage/app/public/images
                $path = $request->file('gambar')->store('images', 'public');
                $barang->gambar = $path;
            }

            $barang->save();

            DB::commit();
            // PERBAIKAN: Mengganti rute 'barang.index' menjadi 'admin.barang.index'
            return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan barang: ' . $e->getMessage())->withInput();
        }
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
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => ['required', 'string', 'max:50', Rule::unique('barang', 'kode_barang')->ignore($barang->id_barang, 'id_barang')],
            'kategori_produk' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'status' => 'required|string|in:aktif,nonaktif',
            'id_unit' => 'required|exists:unit,id_unit',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Proses pembaruan gambar
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($barang->gambar) {
                    Storage::disk('public')->delete($barang->gambar);
                }

                // Simpan gambar baru
                $path = $request->file('gambar')->store('images', 'public');
                $validatedData['gambar'] = $path;
            }

            $barang->update($validatedData);

            DB::commit();
            // PERBAIKAN: Mengganti rute 'barang.index' menjadi 'admin.barang.index'
            return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui barang: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus barang.
     */
    public function destroy(Barang $barang)
    {
        if ($barang->detailPendapatan()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus barang karena sudah memiliki transaksi pendapatan.');
        }

        DB::beginTransaction();
        try {
            // Hapus gambar terkait
            if ($barang->gambar) {
                Storage::disk('public')->delete($barang->gambar);
            }
            
            $barang->riwayatStok()->delete();
            $barang->delete();

            DB::commit();
            // PERBAIKAN: Mengganti rute 'barang.index' menjadi 'admin.barang.index'
            return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }
}