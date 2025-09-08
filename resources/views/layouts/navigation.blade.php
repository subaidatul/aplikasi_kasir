<nav class="shadow-sm p-4 flex justify-between items-center text-black" style="background-color: #88BDB4;">
    <div class="flex items-center">
        <button id="sidebar-toggle" class="hover:text-teal-700 focus:outline-none focus:text-black-700 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <div class="text-xl font-bold">
            @yield('page_title', 'Dashboard')
        </div>
    </div>
    
    @auth
    <div class="flex items-center">
        <img src="{{ asset('storage/images/logo betek.png') }}" alt="Logo Betek" class="w-16 h-16 mr-4">
        <div class="relative group">
            <button class="flex items-center p-2 rounded-lg hover:bg-teal-700 hover:text-white transition-colors duration-200 font-bold focus:outline-none">
                <span class="mr-2">{{ Auth::user()->name }}</span>
                <svg class="w-4 h-4 transition-transform transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div class="absolute right-0 mt-2 w-48 hover:text-white rounded-md shadow-lg py-1 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                <a href="{{ route('logout') }}" 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="block px-4 py-2 text-sm text-black p-2 rounded-lg hover:bg-teal-700 font-bold">
                    Logout
                </a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    @endauth
</nav>