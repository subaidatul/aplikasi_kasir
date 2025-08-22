<aside class="w-64 bg-gray-800 text-white p-4 fixed top-0 left-0 min-h-screen z-50">
    <div class="text-2xl font-bold mb-6 text-center">Kasir Rest Area</div>
    <nav>
        <ul>
            <li class="mb-2">
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-10v10a1 1 0 001 1h3m-5-11v11a1 1 0 01-1 1h-1a1 1 0 01-1-1v-11a1 1 0 011-1h1a1 1 0 011 1z"></path></svg>
                    Home/Dashboard
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('barang.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Barang(item)
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('pendapatan.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.666 0-3.333 0-5 0M12 12c-1.666 0-3.333 0-5 0M12 16c-1.666 0-3.333 0-5 0M15 8h4a1 1 0 011 1v2a1 1 0 01-1 1h-4m-12 0h-4a1 1 0 00-1 1v2a1 1 0 001 1h4m7-4V7a1 1 0 011-1h2a1 1 0 011 1v1m-7 4v3a1 1 0 001 1h2a1 1 0 001-1v-3m-7 4v3a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3m7 4V7a1 1 0 00-1-1h-2a1 1 0 00-1 1v1"></path></svg>
                    Pendapatan
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('pengeluaran.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.666 0-3.333 0-5 0M12 12c-1.666 0-3.333 0-5 0M12 16c-1.666 0-3.333 0-5 0M15 8h4a1 1 0 011 1v2a1 1 0 01-1 1h-4m-12 0h-4a1 1 0 00-1 1v2a1 1 0 001 1h4m7-4V7a1 1 0 011-1h2a1 1 0 011 1v1m-7 4v3a1 1 0 001 1h2a1 1 0 001-1v-3m-7 4v3a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3m7 4V7a1 1 0 00-1-1h-2a1 1 0 00-1 1v1"></path></svg>
                    Pengeluaran
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('stok.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16h4m12-4h-4m-4 0h-4m-4-4v4m0 4v4m4-8h4m4 0h4m0 4v4m0-8V4m-8 4h4m4 0h4m0-4V4m0 8h4"></path></svg>
                    Stok
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('unit.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h2v-2h-2m2-3v-2h-2v2m-3 3h-2v-2h2m-3 2h-2v-2h2m-3 3v-2h-2v2m-3-3h-2v-2h2m-3 2v-2h-2v2"></path></svg>
                    Unit Usaha
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('rekap.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m0 0h18m0 0v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6m0 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v14"></path></svg>
                    Rekap
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('struk.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12h.01M12 7h.01M12 17h.01M19 12a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Struk
                </a>
            </li>
        </ul>
    </nav>
</aside>