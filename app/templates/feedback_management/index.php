@extends('layouts.admin')

@section('title')
Manajemen Feedback
@endsection

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Analisa Kepuasan Klien</h2>
            <p class="text-gray-500">Pantau performa dan feedback dari klien.</p>
        </div>
        
        <div class="bg-white p-1 rounded-lg border border-gray-200 flex">
            {{-- PERBAIKAN: Hapus 'onclick', gunakan ID dan data-target --}}
            <button id="tab-btn-total" data-target="total" class="tab-button px-4 py-2 text-sm font-medium rounded-md transition-colors bg-primary text-white shadow-sm">
                <ion-icon name="pie-chart-outline" class="mr-1"></ion-icon> Ringkasan
            </button>
            <button id="tab-btn-reviews" data-target="reviews" class="tab-button px-4 py-2 text-sm font-medium rounded-md transition-colors text-gray-600 hover:bg-gray-50">
                <ion-icon name="list-outline" class="mr-1"></ion-icon> Daftar Review
            </button>
        </div>
    </div>

    <div id="tab-content-total" class="tab-content space-y-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card-df p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Rata-rata Rating</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-gray-900">{{ number_format($summary['avg_rating_all'], 1) }}</span>
                        <span class="text-sm text-gray-500">/ 5.0</span>
                    </div>
                </div>
                <div class="p-3 bg-yellow-100 text-yellow-600 rounded-full">
                    <ion-icon name="star" class="text-2xl"></ion-icon>
                </div>
            </div>
            
            <div class="card-df p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Feedback</p>
                    <span class="text-3xl font-bold text-gray-900">{{ $summary['total_feedback'] }}</span>
                </div>
                <div class="p-3 bg-blue-100 text-blue-600 rounded-full">
                    <ion-icon name="chatbox-outline" class="text-2xl"></ion-icon>
                </div>
            </div>

            <div class="card-df p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Baru (30 Hari)</p>
                    <span class="text-3xl font-bold text-gray-900">+{{ $summary['total_new_30'] }}</span>
                </div>
                <div class="p-3 bg-green-100 text-green-600 rounded-full">
                    <ion-icon name="trending-up-outline" class="text-2xl"></ion-icon>
                </div>
            </div>

            <div class="card-df p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Avg Rating (30 Hari)</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-gray-900">{{ number_format($summary['avg_rating_30'], 1) }}</span>
                    </div>
                </div>
                <div class="p-3 bg-purple-100 text-purple-600 rounded-full">
                    <ion-icon name="analytics-outline" class="text-2xl"></ion-icon>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-1 card-df p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Rating</h3>
                <div class="flex items-end gap-2 mb-2">
                    <span class="text-4xl font-bold text-gray-900">{{ number_format($summary['avg_rating_all'], 1) }}</span>
                    <div class="flex text-yellow-400 mb-2">
                        @for($i=1; $i<=5; $i++)
                            <ion-icon name="{{ $i <= round($summary['avg_rating_all']) ? 'star' : 'star-outline' }}"></ion-icon>
                        @endfor
                    </div>
                    <span class="text-sm text-gray-500 mb-2 ml-auto">dari {{ $summary['total_feedback'] }} review</span>
                </div>
                
                <div class="h-64 w-full">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>

            <div class="lg:col-span-2 card-df p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Rating (Tahun Ini)</h3>
                <div class="h-72 w-full">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

    </div>
    <div id="tab-content-reviews" class="tab-content space-y-6 hidden">
        <div class="card-df overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Klien / Sekolah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ulasan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(empty($reviews))
                            <tr><td colspan="5" class="p-6 text-center text-gray-500">Belum ada data ulasan.</td></tr>
                        @else
                            @foreach($reviews as $review)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $review['nama_klien'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $review['nama_sekolah'] }} ({{ $review['nomor_order'] }})</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex text-yellow-400 text-sm">
                                        @for($i=1; $i<=5; $i++)
                                            <ion-icon name="{{ $i <= $review['rating'] ? 'star' : 'star-outline' }}"></ion-icon>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 line-clamp-2 max-w-md">
                                        {{ strip_tags($review['komentar']) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $review['tgl_review'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ url('/order/' . $review['id_order_produksi'] . '/detail#feedback') }}" class="text-primary hover:text-blue-900">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination (Jika ada) --}}
            @if(isset($pagination) && $pagination['last_page'] > 1)
            <div class="px-6 py-3 border-t border-gray-200 flex justify-between items-center">
                <span class="text-sm text-gray-700">
                    Hal {{ $pagination['current_page'] }} dari {{ $pagination['last_page'] }}
                </span>
            </div>
            @endif
        </div>
    </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    $(document).ready(function() {
        
        // ==========================================================
        //  1. LOGIKA TAB (PERBAIKAN)
        // ==========================================================
        $('.tab-button').on('click', function() {
            const target = $(this).data('target'); // Ambil 'total' atau 'reviews'

            // Sembunyikan semua konten tab
            $('.tab-content').addClass('hidden');
            // Tampilkan konten yang dipilih
            $('#tab-content-' + target).removeClass('hidden');

            // Reset style semua tombol
            $('.tab-button').removeClass('bg-primary text-white shadow-sm').addClass('text-gray-600 hover:bg-gray-50');
            
            // Aktifkan tombol yang diklik
            $(this).removeClass('text-gray-600 hover:bg-gray-50').addClass('bg-primary text-white shadow-sm');
        });


        // ==========================================================
        //  2. LOGIKA CHART (PERBAIKAN)
        // ==========================================================
        
        // --- Chart 1: Distribution (Horizontal Bar) ---
        const distData = {!! json_encode($distribution) !!};
        const ctxDist = document.getElementById('distributionChart').getContext('2d');
        
        if (ctxDist) {
            new Chart(ctxDist, {
                type: 'bar',
                data: {
                    // Label dibalik agar bintang 5 di atas
                    labels: ['5 Bintang', '4 Bintang', '3 Bintang', '2 Bintang', '1 Bintang'], 
                    datasets: [{
                        label: 'Jumlah Review',
                        data: [distData[5], distData[4], distData[3], distData[2], distData[1]],
                        backgroundColor: [
                            '#22c55e', // 5 - Green
                            '#84cc16', // 4 - Lime
                            '#eab308', // 3 - Yellow
                            '#f97316', // 2 - Orange
                            '#ef4444'  // 1 - Red
                        ],
                        borderRadius: 4,
                        barThickness: 20
                    }]
                },
                options: {
                    indexAxis: 'y', // Horizontal Bar
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { 
                            beginAtZero: true, 
                            grid: { display: false } 
                        },
                        y: { 
                            grid: { display: false } 
                        }
                    }
                }
            });
        }

        // --- Chart 2: Monthly Trend (Line) ---
        const trendData = {!! json_encode($trend) !!};
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        
        if (ctxTrend) {
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Rata-rata Rating',
                        data: trendData,
                        borderColor: '#4F46E5', // Primary Color (Indigo)
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.4, // Garis lengkung
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: false, 
                            min: 0, max: 5, 
                            ticks: { stepSize: 1 } 
                        }
                    }
                }
            });
        }

    });
</script>
@endpush