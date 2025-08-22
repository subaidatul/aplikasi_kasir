<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Stok;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('unit')->get();

        $stokData = $barangs->map(function ($barang) {
            $stokMasuk = Stok::where('id_barang', $barang->id_barang)->sum('stok_masuk');
            $stokKeluar = Stok::where('id_barang', $barang->id_barang)->sum('stok_keluar');
            $sisa = $barang->stok; 
            $total = $sisa * $barang->harga_jual;

            return [
                'id_barang' => $barang->id_barang,
                'nama_barang' => $barang->nama_barang,
                'satuan' => $barang->satuan,
                'unit' => optional($barang->unit)->nama_unit,
                'stok_masuk' => $stokMasuk,
                'stok_keluar' => $stokKeluar,
                'sisa' => $sisa,
                'harga' => $barang->harga_jual,
                'total' => $total,
            ];
        });

        return view('stok.index', compact('stokData'));
    }

    public function create()
    {
        $barangs = Barang::all();
        $units = Unit::all();
        return view('stok.create', compact('barangs', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'jumlah_stok' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'id_unit' => 'required|exists:unit,id_unit',
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::find($request->id_barang);
            $barang->stok += $request->jumlah_stok;
            $barang->save();

            Stok::create([
                'id_barang' => $barang->id_barang,
                'id_unit' => $request->id_unit,
                'no_transaksi' => 'STK-' . time(),
                'tanggal' => $request->tanggal,
                'keterangan' => 'Penambahan Stok',
                'stok_masuk' => $request->jumlah_stok,
                'stok_keluar' => 0,
                'sisa_stok' => $barang->stok,
            ]);

            DB::commit();
            return redirect()->route('stok.index')->with('success', 'Stok berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan stok: ' . $e->getMessage())->withInput();
        }
    }
}