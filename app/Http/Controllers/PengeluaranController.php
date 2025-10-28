<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\DetailPengeluaran;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    public function index()
    {
        $pengeluarans = Pengeluaran::with('unit')->latest()->get();
        // PERBAIKAN: Mengganti rute di view
        return view('pengeluaran.index', compact('pengeluarans'));
    }

    public function create()
    {
        $units = Unit::all();
        // PERBAIKAN: Mengganti rute di view
        return view('pengeluaran.create', compact('units'));
    }

    public function store(Request $request)
    {
        return $this->processPengeluaran($request);
    }

    public function edit(Pengeluaran $pengeluaran)
    {
        $pengeluaran->load('details', 'unit');
        $units = Unit::all();
        // PERBAIKAN: Mengganti rute di view
        return view('pengeluaran.edit', compact('pengeluaran', 'units'));
    }

    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        return $this->processPengeluaran($request, $pengeluaran);
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        DB::beginTransaction();
        try {
            $pengeluaran->details()->delete();
            $pengeluaran->delete();
            DB::commit();
            // PERBAIKAN: Mengarahkan ke rute yang benar
            return redirect()->route('admin.pengeluaran.index')->with('success', 'Pengeluaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pengeluaran: ' . $e->getMessage());
        }
    }

    private function processPengeluaran(Request $request, ?Pengeluaran $pengeluaran = null)
    {
        // PERBAIKAN: Memastikan validasi yang tepat untuk semua input
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
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

            // PERBAIKAN: Menggunakan id_unit dari user yang sedang login
            // dan menambahkan deskripsi dari request
            $baseData = [
                'id_user' => Auth::id(),
                'id_unit' => Auth::user()->id_unit,
                'tanggal' => now(), // Perbaikan: Menggunakan now() untuk menyimpan tanggal dan waktu
                'deskripsi' => $request->input('deskripsi', ''),
                'total' => $totalPengeluaran,
            ];

            if ($pengeluaran) {
                // Logika untuk UPDATE
                $pengeluaran->update($baseData);

                // Sinkronisasi detail pengeluaran
                $submittedDetailIds = collect($validatedData['items'])->pluck('id_detail_pengeluaran')->filter()->toArray();
                $pengeluaran->details()->whereNotIn('id_detail_pengeluaran', $submittedDetailIds)->delete();

                foreach ($validatedData['items'] as $item) {
                    $detailData = [
                        'nama_keperluan' => $item['nama_keperluan'],
                        'jumlah' => $item['jumlah'],
                        'total' => $item['harga'] * $item['jumlah'],
                    ];

                    if (isset($item['id_detail_pengeluaran'])) {
                        $pengeluaran->details()->where('id_detail_pengeluaran', $item['id_detail_pengeluaran'])->update($detailData);
                    } else {
                        $pengeluaran->details()->create($detailData);
                    }
                }
                $action = 'diperbarui';
            } else {
                // Logika untuk CREATE
                $baseData['no_pengeluaran'] = 'KEL-' . now()->timestamp;
                $pengeluaran = Pengeluaran::create($baseData);
                
                $detailsData = collect($validatedData['items'])->map(function ($item) {
                    return [
                        'nama_keperluan' => $item['nama_keperluan'],
                        'jumlah' => $item['jumlah'],
                        'total' => $item['harga'] * $item['jumlah'],
                    ];
                })->toArray();
                
                $pengeluaran->details()->createMany($detailsData);
                $action = 'disimpan';
            }
            
            DB::commit();
            // PERBAIKAN: Mengarahkan ke rute yang benar
            return redirect()->route('admin.pengeluaran.index')->with('success', "Transaksi pengeluaran berhasil $action.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengeluaran: ' . $e->getMessage())->withInput();
        }
    }
}