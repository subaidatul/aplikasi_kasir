<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\DetailPengeluaran;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    /**
     * Menampilkan daftar semua pengeluaran.
     */
    public function index()
    {
        $pengeluarans = Pengeluaran::with('unit')->latest()->get();
        return view('pengeluaran.index', compact('pengeluarans'));
    }

    /**
     * Menampilkan form untuk menambah pengeluaran baru.
     */
    public function create()
    {
        $units = Unit::all();
        return view('pengeluaran.create', compact('units'));
    }

    /**
     * Menyimpan pengeluaran baru.
     */
    public function store(Request $request)
    {
        return $this->processPengeluaran($request);
    }

    /**
     * Menampilkan form untuk mengedit pengeluaran.
     */
    public function edit(Pengeluaran $pengeluaran)
    {
        $pengeluaran->load('details', 'unit');
        $units = Unit::all();
        return view('pengeluaran.edit', compact('pengeluaran', 'units'));
    }

    /**
     * Memperbarui data pengeluaran yang sudah ada.
     */
    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        return $this->processPengeluaran($request, $pengeluaran);
    }

    /**
     * Menghapus data pengeluaran.
     */
    public function destroy(Pengeluaran $pengeluaran)
    {
        DB::beginTransaction();
        try {
            // Hapus detail pengeluaran terkait
            $pengeluaran->details()->delete();
            // Hapus pengeluaran utama
            $pengeluaran->delete();

            DB::commit();
            return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pengeluaran: ' . $e->getMessage());
        }
    }

    /**
     * Metode privat untuk memproses penyimpanan atau pembaruan pengeluaran.
     *
     * @param Request $request
     * @param Pengeluaran|null $pengeluaran
     * @return \Illuminate\Http\RedirectResponse
     */
    private function processPengeluaran(Request $request, ?Pengeluaran $pengeluaran = null)
    {
        // Validasi input
        $validatedData = $request->validate([
            'id_unit' => 'required|exists:unit,id_unit',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string',
            'items' => 'required|array',
            'items.*.nama_keperluan' => 'required|string|max:255',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalPengeluaran = 0;
            foreach ($validatedData['items'] as $item) {
                $totalPengeluaran += $item['harga'] * $item['jumlah'];
            }

            $baseData = [
                'id_unit' => $validatedData['id_unit'],
                'tanggal' => $validatedData['tanggal'],
                'deskripsi' => $validatedData['deskripsi'],
                'total' => $totalPengeluaran,
            ];

            if ($pengeluaran) {
                // Hapus detail lama sebelum membuat yang baru
                $pengeluaran->details()->delete();
                $pengeluaran->update($baseData);
            } else {
                $baseData['no_pengeluaran'] = 'KEL-' . time();
                $pengeluaran = Pengeluaran::create($baseData);
            }

            // Buat detail pengeluaran baru
            foreach ($validatedData['items'] as $item) {
                DetailPengeluaran::create([
                    'id_pengeluaran' => $pengeluaran->id_pengeluaran,
                    'nama_keperluan' => $item['nama_keperluan'],
                    'jumlah' => $item['jumlah'],
                    'total' => $item['harga'] * $item['jumlah'],
                ]);
            }

            DB::commit();
            $message = $pengeluaran ? 'diperbarui' : 'disimpan';
            return redirect()->route('pengeluaran.index')->with('success', "Transaksi pengeluaran berhasil $message!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengeluaran: ' . $e->getMessage())->withInput();
        }
    }
}