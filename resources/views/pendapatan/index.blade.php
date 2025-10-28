@extends('layouts.app')

@section('page_title', 'Data Pendapatan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Data Pendapatan</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <a href="{{ route('pendapatan.create') }}"
            class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded mb-4 inline-block">
            Tambah Pendapatan
        </a>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
                            Pendapatan</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($pendapatans as $pendapatan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pendapatan->no_pendapatan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($pendapatan->tanggal)->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pendapatan->unit->nama_unit }}</td>

                            {{-- Kolom yang diperbaiki untuk menampilkan item --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($pendapatan->detailPendapatan->isNotEmpty())
                                    @foreach ($pendapatan->detailPendapatan as $detail)
                                        @if ($detail->barang)
                                            {{-- Menampilkan item dari relasi (untuk Unit 1, Warung & Toko) --}}
                                            {{ $detail->barang->nama_barang }} (x{{ $detail->jumlah }})@if (!$loop->last)
                                                ,
                                            @endif
                                        @elseif ($detail->nama_barang_manual)
                                            {{-- Menampilkan item yang dimasukkan secara manual (untuk Unit 6, Kemah Rimbun) --}}
                                            {{ $detail->nama_barang_manual }} (x{{ $detail->jumlah }})@if (!$loop->last)
                                                ,
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    {{-- Menampilkan deskripsi jika tidak ada detail item (untuk unit lain) --}}
                                    {{ $pendapatan->deskripsi }}
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($pendapatan->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('pendapatan.edit', $pendapatan->id_pendapatan) }}"
                                    class="text-indigo-600 hover:text-indigo-900 font-bold mr-2">
                                    Edit
                                </a>
                                <a href="{{ route('pendapatan.cetakStruk', $pendapatan->id_pendapatan) }}" target="_blank"
                                    class="text-blue-700 hover:text-blue-900 font-bold mr-2">
                                    Cetak Struk
                                </a>
                                <form action="{{ route('pendapatan.destroy', $pendapatan->id_pendapatan) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Anda yakin ingin menghapus data ini? Aksi ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection