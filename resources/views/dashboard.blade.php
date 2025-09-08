@extends('layouts.app')

@section('page_title', 'Dashboard')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Selamat Datang di Aplikasi Pembukuan Rest Area! 👋</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-gray-500 text-sm font-semibold uppercase tracking-wide mb-2">Total Pendapatan</h2>
            <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-gray-500 text-sm font-semibold uppercase tracking-wide mb-2">Total Pengeluaran</h2>
            <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-gray-500 text-sm font-semibold uppercase tracking-wide mb-2">Laba Bersih</h2>
            <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($labaBersih, 0, ',', '.') }}</p>
            <p class="text-gray-500 text-xs font-normal mt-2">Laba Bersih = Harga Jual - Harga Beli - Pengeluaran</p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Grafik Bulanan: Pendapatan, Pengeluaran, & Pengunjung</h2>
        <div class="h-96">
            <canvas id="balanceChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('balanceChart').getContext('2d');
        const balanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($grafikData['labels']),
                datasets: [
                    {
                        label: 'Pendapatan (Rp)',
                        data: @json($grafikData['pendapatan']),
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        yAxisID: 'uang',
                    },
                    {
                        label: 'Pengeluaran (Rp)',
                        data: @json($grafikData['pengeluaran']),
                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1,
                        yAxisID: 'uang',
                    },
                    {
                        label: 'Pengunjung (Orang)',
                        data: @json($grafikData['pengunjung']),
                        backgroundColor: 'rgba(16, 185, 129, 0.5)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        yAxisID: 'pengunjung',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: false,
                    },
                    uang: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Uang (Rp)'
                        }
                    },
                    pengunjung: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Pengunjung'
                        },
                        grid: {
                            drawOnChartArea: false, // Menghilangkan garis grid untuk yAxis kanan
                        },
                    }
                }
            }
        });
    </script>
@endsection