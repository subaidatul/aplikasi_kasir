@extends('layouts.app')

@section('page_title', 'Data Pengeluaran')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Data Pengeluaran</h1>

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

    <div class="bg-white p-6 rounded-lg shadow-md">
        <a href="{{ route('admin.pengeluaran.create') }}"
             class="bg-[#88BDB4] hover:bg-teal-700 text-black hover:text-white font-bold py-2 px-4 rounded mb-4 inline-block">
            Tambah Pengeluaran
        </a>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
                            Pengeluaran</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keperluan
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($pengeluarans as $pengeluaran)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pengeluaran->no_pengeluaran }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pengeluaran->details->isNotEmpty())
                                    {{ $pengeluaran->details->pluck('nama_keperluan')->implode(', ') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($pengeluaran->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                                <a href="{{ route('admin.pengeluaran.edit', $pengeluaran->id_pengeluaran) }}"
                                    class="text-indigo-600 hover:text-indigo-900 font-bold">
                                    Edit
                                </a>
                                <a href="{{ route('admin.struk.show', ['jenis' => 'pengeluaran', 'id' => $pengeluaran->id_pengeluaran]) }}"
                                    target="_blank" class="text-blue-600 hover:text-blue-900 font-bold">
                                    Cetak Struk
                                </a>

                                <form action="{{ route('admin.pengeluaran.destroy', $pengeluaran->id_pengeluaran) }}"
                                    method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?');">
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