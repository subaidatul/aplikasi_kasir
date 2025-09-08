<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Pembukuan Rest Area</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        /* Transisi untuk animasi */
        .sidebar {
            transition: width 0.3s ease, padding 0.3s ease;
        }

        .main-content {
            transition: margin-left 0.3s ease;
        }

        .sidebar-text {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar tetap di kiri --}}
        @include('layouts.sidebar')

        {{-- Konten utama digeser ke kanan dengan padding --}}
        <div id="main-content" class="flex-1 flex flex-col pl-64 main-content">
            @include('layouts.navigation')

            <main class="flex-1 p-6">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>

            {{-- KODE FOOTER BARU DI SINI, DI DALAM main-content --}}
            <footer class="w-full p-4 text-black font-bold text-center" style="background-color: #88BDB4;">
                <span>&copy;2025PKLUNUJA</span>
            </footer>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleButton = document.getElementById('sidebar-toggle');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('w-64');
            sidebar.classList.toggle('w-20');

            if (sidebar.classList.contains('w-20')) {
                mainContent.classList.remove('pl-64');
                mainContent.classList.add('pl-20');
                sidebarTexts.forEach(text => {
                    text.classList.add('hidden');
                });
            } else {
                mainContent.classList.remove('pl-20');
                mainContent.classList.add('pl-64');
                sidebarTexts.forEach(text => {
                    text.classList.remove('hidden');
                });
            }
        });
    </script>
</body>

</html>