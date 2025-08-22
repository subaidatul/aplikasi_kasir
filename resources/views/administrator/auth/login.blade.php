<!DOCTYPE html>

<html lang="id">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <!-- Tailwind CSS untuk styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">

    <div class="w-full max-w-md p-8 space-y-8 bg-white rounded-xl shadow-lg">

        <h2 class="text-3xl font-bold text-center text-gray-900">

            Masuk ke Akun Anda

            </h2>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">

                {{ session('success') }}

                </div>
        @endif

        @if ($errors->any())

            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                role="alert">

                <ul>

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                    </ul>

                </div>

            @endif

        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">

            @csrf

            <div>

                <label for="login" class="block text-sm font-medium text-gray-700">

                    Nama Pengguna atau Email

                    </label>

                <div class="mt-1">

                    <input id="login" name="login" type="text" value="{{ old('login') }}"
                        required
                        class="relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Masukkan nama pengguna atau email Anda">

                    </div>

                </div>

            <div>

                <label for="password" class="block text-sm font-medium text-gray-700">

                    Password

                    </label>

                <div class="mt-1">

                    <input id="password" name="password" type="password" required
                        
                        class="relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Masukkan password Anda">

                    </div>

                </div>

            <div>

                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

                    Masuk

                    </button>

                </div>

            </form>

        <div class="text-sm text-center">

            <a href="{{ route('register.form') }}"
                class="font-medium text-indigo-600 hover:text-indigo-500">

                Belum punya akun? Daftar di sini.

                </a>

            </div>

        </div>

</body>

</html>
