<nav class="bg-white shadow-sm p-4 flex justify-between items-center">
    <div class="text-xl font-bold text-gray-800">
        @yield('page_title', 'Dashboard')
    </div>
    
    @auth
    <div class="relative group">
        <button class="flex items-center text-gray-600 hover:text-gray-800 focus:outline-none">
            <span class="mr-2">{{ Auth::user()->name }}</span>
            <svg class="w-4 h-4 transition-transform transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Logout
            </a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
    @endauth
</nav>