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
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800" id="chart-title">GRAFIK</h2>
            <div class="flex space-x-2">
                <button id="monthlyBtn" class="px-4 py-2 rounded-md font-semibold text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Bulanan</button>
                <button id="yearlyBtn" class="px-4 py-2 rounded-md font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">Tahunan</button>
                <button id="downloadBtn" class="px-4 py-2 rounded-md font-semibold text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">Unduh</button>
            </div>
        </div>
        <div class="h-96">
            <canvas id="balanceChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data dari backend
        const monthlyData = @json($grafikDataBulanan);
        const yearlyData = @json($grafikDataTahunan);

        const ctx = document.getElementById('balanceChart').getContext('2d');
        let currentChart; // Variabel untuk menyimpan instance chart saat ini

        // Fungsi utama untuk membuat dan memperbarui grafik
        function createChart(data, period) {
            if (currentChart) {
                currentChart.destroy();
            }

            const chartTitleText = period === 'monthly' ? 'Grafik Bulanan: Pendapatan, Pengeluaran, & Pengunjung' : 'Grafik Tahunan: Pendapatan, Pengeluaran, & Pengunjung';

            currentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Pendapatan (Rp)',
                            data: data.pendapatan,
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1,
                            yAxisID: 'uang',
                        },
                        {
                            label: 'Pengeluaran (Rp)',
                            data: data.pengeluaran,
                            backgroundColor: 'rgba(239, 68, 68, 0.5)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 1,
                            yAxisID: 'uang',
                        },
                        {
                            label: 'Pengunjung (Orang)',
                            data: data.pengunjung,
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
                            ticks: {
                                callback: function(value, index, ticks) {
                                    const label = this.getLabelForValue(value);
                                    if (period === 'monthly') {
                                        const date = new Date(label + '-01');
                                        return date.toLocaleString('id-ID', { month: 'short', year: 'numeric' });
                                    }
                                    return label;
                                }
                            }
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
                                drawOnChartArea: false,
                            },
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: chartTitleText,
                            font: {
                                size: 18,
                                weight: 'bold'
                            },
                            color: '#4A5568'
                        }
                    }
                }
            });
        }

        // Fungsi untuk menangani unduhan grafik
        function downloadChart() {
            const originalCanvas = document.getElementById('balanceChart');

            // Buat kanvas sementara untuk menggambar ulang grafik dengan latar belakang
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = originalCanvas.width;
            tempCanvas.height = originalCanvas.height;
            const tempCtx = tempCanvas.getContext('2d');

            // Menggambar latar belakang putih pada kanvas sementara
            tempCtx.fillStyle = '#FFFFFF';
            tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

            // Menggambar grafik dari kanvas asli ke kanvas sementara
            tempCtx.drawImage(originalCanvas, 0, 0);

            // Dapatkan URL gambar dari kanvas sementara
            const imageURL = tempCanvas.toDataURL('image/png');

            // Buat tautan unduhan
            const downloadLink = document.createElement('a');
            downloadLink.href = imageURL;
            downloadLink.download = 'grafik-rest-area.png';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }

        // Tampilkan grafik bulanan saat pertama kali halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            createChart(monthlyData, 'monthly');
        });

        // Event listener untuk tombol bulanan
        document.getElementById('monthlyBtn').addEventListener('click', () => {
            createChart(monthlyData, 'monthly');
            document.getElementById('monthlyBtn').classList.remove('bg-gray-200', 'text-gray-700');
            document.getElementById('monthlyBtn').classList.add('bg-blue-500', 'text-white');
            document.getElementById('yearlyBtn').classList.remove('bg-blue-500', 'text-white');
            document.getElementById('yearlyBtn').classList.add('bg-gray-200', 'text-gray-700');
        });

        // Event listener untuk tombol tahunan
        document.getElementById('yearlyBtn').addEventListener('click', () => {
            createChart(yearlyData, 'yearly');
            document.getElementById('yearlyBtn').classList.remove('bg-gray-200', 'text-gray-700');
            document.getElementById('yearlyBtn').classList.add('bg-blue-500', 'text-white');
            document.getElementById('monthlyBtn').classList.remove('bg-blue-500', 'text-white');
            document.getElementById('monthlyBtn').classList.add('bg-gray-200', 'text-gray-700');
        });

        // Event listener untuk tombol unduh
        document.getElementById('downloadBtn').addEventListener('click', downloadChart);

    </script>
@endsection