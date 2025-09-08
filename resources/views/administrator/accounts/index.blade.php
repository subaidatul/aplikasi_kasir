@extends('layouts.app')

@section('page_title', 'Manajemen Akun')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Daftar Akun Pengguna</h2>

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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($accounts as $account)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $account->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $account->username }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $account->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $account->role }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{-- Tampilkan tombol hapus hanya jika akun bukan milik pengguna yang sedang login --}}
                        @if($account->id_user !== Auth::user()->id_user)
                            {{-- Perbaikan: Ganti rute 'account.destroy' menjadi 'admin.account.destroy' --}}
                            <form action="{{ route('admin.account.destroy', $account->id_user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                            </form>
                        @else
                            <span class="text-gray-400">Akun Anda</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection