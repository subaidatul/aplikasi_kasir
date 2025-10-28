@extends('layouts.app')

@section('page_title', 'Daftar Stok')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Stok</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">

        <div class="mb-4 flex gap-2">
            {{-- Perbaikan: Tambah awalan 'admin.' pada rute --}}
            <a href="{{ route('admin.stok.create') }}"
                class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded inline-block transition-colors duration-300">
                <i class="fas fa-plus mr-2"></i>Tambah Stok
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TANGGAL
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UNIT
                            USAHA</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA
                            BARANG</th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">STOK
                            MASUK</th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">STOK
                            KELUAR</th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">SISA
                            STOK</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            KETERANGAN</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($stoks as $stok)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ ($stoks->currentPage() - 1) * $stoks->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($stok->tanggal)->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $stok->unit->nama_unit ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap flex items-center">
                                {{ $stok->barang->nama_barang ?? 'N/A' }}
                                @if ($stok->sisa_stok <= 0)
                                    <span
                                        class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Stok Habis!
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                {{ $stok->stok_masuk > 0 ? $stok->stok_masuk : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                {{ $stok->stok_keluar > 0 ? $stok->stok_keluar : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">{{ $stok->sisa_stok }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $stok->keterangan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{-- Perbaikan: Tambah awalan 'admin.' pada rute --}}
                                {{-- <a href="{{ route('admin.stok.edit', $stok->id_stok) }}"
                                    class="text-indigo-600 hover:text-indigo-900 mr-2 transition-colors duration-300 font-bold">Edit</a>
                                {{-- Perbaikan: Tambah awalan 'admin.' pada rute --}}
                                <form action="{{ route('admin.stok.destroy', $stok->id_stok) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan stok ini? Ini akan mengubah total stok barang!');"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 transition-colors duration-300 font-bold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $stoks->links() }}
        </div>
    </div>
@endsection