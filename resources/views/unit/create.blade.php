@extends('layouts.app')

@section('page_title', 'Tambah Unit')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Tambah Unit Baru</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('unit.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="nama_unit" class="block text-sm font-medium text-gray-700">Nama Unit</label>
                <input type="text" name="nama_unit" id="nama_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            
            <div class="mb-4">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Simpan Unit
                </button>
            </div>
        </form>
    </div>
@endsection