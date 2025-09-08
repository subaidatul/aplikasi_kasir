@extends('layouts.app')

@section('page_title', 'Daftar Unit')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Daftar Unit</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Perbaikan: Tambah awalan 'admin.' pada rute --}}
        <a href="{{ route('admin.unit.create') }}" class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded mb-4 inline-block">
            Tambah Unit
        </a>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Unit</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($units as $index => $unit)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $unit->nama_unit }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $unit->deskripsi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{-- Perbaikan: Tambah awalan 'admin.' pada rute --}}
                                <a href="{{ route('admin.unit.edit', $unit->id_unit) }}" class="text-indigo-600 hover:text-indigo-900 font-bold mr-2">Edit</a>
                                {{-- Perbaikan: Tambah awalan 'admin.' pada rute --}}
                                <form action="{{ route('admin.unit.destroy', $unit->id_unit) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection