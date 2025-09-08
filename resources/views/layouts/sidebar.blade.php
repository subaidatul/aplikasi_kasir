<aside id="sidebar" class="w-64 text-gray-900 p-4 fixed top-0 left-0 min-h-screen z-50 flex flex-col sidebar"
    style="background-color: #88BDB4;">

    {{-- Header Sidebar --}}
    <div class="flex items-center text-2xl font-bold mb-6">
        <i class="fa-solid fa-store mr-2"></i>
        <span class="sidebar-text">Kasir Rest Area</span>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 overflow-y-auto">
        <ul class="space-y-2 font-semibold">
            @php
                $isAdmin = Auth::check() && Auth::user()->role === 'admin';
                $disabledLink = 'href=\'#\' onclick="alert(\'Anda bukan admin\'); return false;"';
            @endphp

            {{-- Dashboard --}}
            <li>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center p-2 rounded-lg hover:bg-teal-700 hover:text-white transition-colors duration-200">
                    <i class="fa-solid fa-house mr-2"></i>
                    <span class="font-bold sidebar-text">Dashboard</span>
                </a>
            </li>

            {{-- Pendapatan --}}
            <li>
                <a href="{{ route('pendapatan.index') }}"
                    class="flex items-center p-2 rounded-lg hover:bg-teal-700 hover:text-white transition-colors duration-200">
                    <i class="fa-solid fa-sack-dollar mr-2"></i>
                    <span class="font-bold sidebar-text">Pendapatan</span>
                </a>
            </li>

            {{-- Pengeluaran --}}
            <li>
                <a @if ($isAdmin) href="{{ route('admin.pengeluaran.index') }}" @else {!! $disabledLink !!} @endif
                    class="flex items-center p-2 rounded-lg hover:bg-teal-700 hover:text-white transition-colors duration-200 {{ !$isAdmin ? 'cursor-not-allowed' : '' }}">
                    <i class="fa-solid fa-money-bill-trend-up mr-2"></i>
                    <span class="font-bold sidebar-text">Pengeluaran</span>
                </a>
            </li>

            {{-- Barang --}}
            <li>
                <a @if ($isAdmin) href="{{ route('admin.barang.index') }}" @else {!! $disabledLink !!} @endif
                    class="flex items-center p-2 rounded-lg hover:bg-teal-700 hover:text-white transition-colors duration-200 {{ !$isAdmin ? 'cursor-not-allowed' : '' }}">
                    <i class="fa-solid fa-box-open mr-2"></i>
                    <span class="font-bold sidebar-text">Barang (Item)</span>
                </a>
            </li>

            {{-- Stok --}}
            <li>
                <a @if ($isAdmin) href="{{ route('admin.stok.index') }}" @else {!! $disabledLink !!} @endif
                    class="flex items-center p-2 rounded-lg hover:bg-teal-700 hover:text-white transition-colors duration-200 {{ !$isAdmin ? 'cursor-not-allowed' : '' }}">
                    <i class="fa-solid fa-cubes mr-2"></i>
                    <span class="font-bold sidebar-text">Stok</span>
                </a>
            </li>

            {{-- Unit Usaha --}}
            <li>
                <a @if ($isAdmin) href="{{ route('admin.unit.index') }}" @else {!! $disabledLink !!} @endif
                    class="flex items-center p-2 rounded-lg hover:bg-teal-700 hover:text-white transition-colors duration-200 {{ !$isAdmin ? 'cursor-not-allowed' : '' }}">
                    <i class="fa-solid fa-building mr-2"></i>
                    <span class="font-bold sidebar-text">Unit Usaha</span>
                </a>
            </li>

            {{-- Rekap --}}
            <li>
                <a @if ($isAdmin) href="{{ route('admin.rekap.index') }}" @else {!! $disabledLink !!} @endif
                    class="flex items-center p-2 rounded-lg hover:bg-teal-700 hover:text-white transition-colors duration-200 {{ !$isAdmin ? 'cursor-not-allowed' : '' }}">
                    <i class="fa-solid fa-chart-line mr-2"></i>
                    <span class="font-bold sidebar-text">Rekap</span>
                </a>
            </li>

            {{-- Struk --}}
            <li>
                <a @if ($isAdmin) href="{{ route('admin.struk.index') }}" @else {!! $disabledLink !!} @endif
                    class="flex items-center p-2 rounded-lg hover:bg-teal-700 hover:text-white transition-colors duration-200 {{ !$isAdmin ? 'cursor-not-allowed' : '' }}">
                    <i class="fa-solid fa-receipt mr-2"></i>
                    <span class="font-bold sidebar-text">Struk</span>
                </a>
            </li>

            {{-- Akun --}}
            <li>
                <a @if ($isAdmin) href="{{ route('admin.account.index') }}" @else {!! $disabledLink !!} @endif
                    class="flex items-center p-2 rounded-lg hover:bg-teal-700 hover:text-white transition-colors duration-200 {{ !$isAdmin ? 'cursor-not-allowed' : '' }}">
                    <i class="fa-solid fa-user mr-2"></i>
                    <span class="font-bold sidebar-text">Akun</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>