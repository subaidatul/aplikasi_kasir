<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: url('{{ asset('storage/images/gambar betek.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(136, 189, 180, 0.5);
            z-index: 1;
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .input-container {
            position: relative;
            width: 100%;
        }

        .input-field {
            background-color: #fff;
            border-radius: 0.75rem;
            border: none;
            padding: 0.75rem 1.25rem 0.75rem 2.5rem;
            /* Tambah padding kiri untuk ikon */
            outline: none;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            pointer-events: none;
            z-index: 5;
        }

        .btn-green {
            background-color: #88BDB4;
            color: #252424;
            font-weight: 500;
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-green:hover {
            background-color: #5da897;
        }

        .checkbox-label {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .logo-PE {
            position: absolute;
            top: 4vh;
            left: 12%;
            transform: translateX(-50%);
            z-index: 20;
            opacity: 0.8;
        }

        .logo-pomi {
            position: absolute;
            top: 4vh;
            left: 23%;
            transform: translateX(-50%);
            z-index: 20;
            opacity: 0.8;
        }
        

        .logo-bettek {
            position: absolute;
            top: 4vh;
            right: 15%;
            transform: translateX(50%);
            z-index: 20;
        }
    </style>
</head>

<body>
    <div class="logo-PE">
        <img src="{{ asset('storage/images/logo PE.png') }}" alt="Logo PE" class="h-12 w-auto">
    </div>
    <div class="logo-pomi">
        <img src="{{ asset('storage/images/logo pomi.png') }}" alt="Logo POMI" class="h-12 w-auto">
    </div>
    <div class="logo-bettek">
        <img src="{{ asset('storage/images/logo betek.png') }}" alt="Logo Betek" class="h-20 w-auto">
    </div>

    <div class="z-10 w-full max-w-sm p-8 space-y-6 login-card">
        <div class="text-center">
            @if ($errors->has('login'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ $errors->first('login') }}</span>
                </div>
            @endif
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-16 w-16 mx-auto mb-4 text-gray-500">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h2 class="text-3xl font-bold text-gray-900">
                MASUK
            </h2>
        </div>
        <form class="space-y-4" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="input-container">
                <i class="fa-solid fa-user input-icon"></i>
                <input id="login" name="login" type="text" required placeholder="Email atau Username"
                    value="{{ old('login') }}" class="input-field">
            </div>

            <div class="input-container">
                <i class="fa-solid fa-lock input-icon"></i>
                <input id="password" name="password" type="password" required placeholder="Password"
                    class="input-field">
            </div>

            <div class="flex items-center justify-between mt-2">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox"
                        class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm checkbox-label">
                        Ingatkan saya
                    </label>
                </div>
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-gray-600 hover:text-gray-900">
                        Lupa sandi?
                    </a>
                </div>
            </div>

            <div class="space-y-3 pt-2">
                <button type="submit" class="w-full btn-green">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>
                <a href="{{ route('register.form') }}" class="w-full btn-green">
                    <i class="fas fa-user-plus mr-2"></i> Daftar
                </a>
            </div>
        </form>
    </div>
</body>

</html>
