<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Sandi</title>
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
            color: #252323;
            font-weight: 500;
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-green:hover {
            background-color: #7ab3a6;
        }

        .link-text {
            color: #4a5568;
            font-size: 0.875rem;
            text-decoration: none;
            font-weight: 500;
        }

        .link-text:hover {
            text-decoration: underline;
        }

        .logo-pomi {
            position: absolute;
            top: 4vh;
            left: 15%;
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
    <div class="logo-pomi">
        <img src="{{ asset('storage/images/logo pomi.png') }}" alt="Logo POMI" class="h-12 w-auto">
    </div>
    <div class="logo-bettek">
        <img src="{{ asset('storage/images/logo betek.png') }}" alt="Logo Betek" class="h-20 w-auto">
    </div>

    <div class="z-10 w-full max-w-sm p-8 space-y-6 login-card">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">
                Lupa Sandi
            </h2>
        </div>
        <p class="text-center text-sm text-gray-600">
            Masukkan alamat email Anda, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
        </p>

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="space-y-4" action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="input-container">
                <i class="fa-solid fa-envelope input-icon"></i>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="input-field" placeholder="Masukkan alamat email Anda">
            </div>

            <div class="space-y-3 pt-2">
                <button type="submit" class="w-full btn-green">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Tautan Reset Sandi
                </button>
            </div>
        </form>

        <div class="text-sm text-center">
            <a href="{{ route('login.form') }}" class="link-text">
                Kembali ke halaman login
            </a>
        </div>
    </div>
</body>

</html>