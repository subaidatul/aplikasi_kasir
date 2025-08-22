<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('unit.index', compact('units'));
    }

    public function create()
    {
        return view('unit.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Unit::create($request->all());

        return redirect()->route('unit.index')->with('success', 'Unit berhasil ditambahkan!');
    }

    public function edit(Unit $unit)
    {
        return view('unit.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $unit->update($request->all());

        return redirect()->route('unit.index')->with('success', 'Unit berhasil diupdate!');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('unit.index')->with('success', 'Unit berhasil dihapus!');
    }
}