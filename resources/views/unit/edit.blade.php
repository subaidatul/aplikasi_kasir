@extends('layouts.app')

@section('page_title', 'Edit Unit')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Unit: {{ $unit->nama_unit }}</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- PERBAIKAN: Mengubah route('unit.update') menjadi route('admin.unit.update') --}}
        <form action="{{ route('admin.unit.update', $unit->id_unit) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="nama_unit" class="block text-sm font-medium text-gray-700">Nama Unit</label>
                <input type="text" name="nama_unit" id="nama_unit" value="{{ $unit->nama_unit }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            
            <div class="mb-4">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $unit->deskripsi }}</textarea>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded">
                    Update Unit
                </button>
            </div>
        </form>
    </div>
@endsection