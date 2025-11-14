@extends('layouts.admin')

@section('title')
Dashboard Marketing
@endsection

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card-df">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="txt-title-df text-yellow-600">Prospek Aktif Saya</p>
                        <p class="text-3xl font-semibold text-gray-900">{{ $stats['prospek_aktif'] }}</p>
                    </div>
                    <div class="bg-yellow-100 text-yellow-600 w-16 h-16 rounded-full flex items-center justify-center">
                        <ion-icon name="chatbubbles-outline" class="text-3xl"></ion-icon>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-df">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="txt-title-df text-green-600">Konversi (30 Hari)</p>
                        <p class="text-3xl font-semibold text-gray-900">{{ $stats['konversi_30_hari'] }}</p>
                    </div>
                    <div class="bg-green-100 text-green-600 w-16 h-16 rounded-full flex items-center justify-center">
                        <ion-icon name="checkmark-done-outline" class="text-3xl"></ion-icon>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-df">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="txt-title-df text-red-600">Gagal (30 Hari)</p>
                        <p class="text-3xl font-semibold text-gray-900">{{ $stats['gagal_30_hari'] }}</p>
                    </div>
                    <div class="bg-red-100 text-red-600 w-16 h-16 rounded-full flex items-center justify-center">
                        <ion-icon name="close-circle-outline" class="text-3xl"></ion-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="card-df">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Corong Prospek Saya</h3>
                    <div class="h-80 w-full">
                        <canvas id="prospectStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-2">
            <div class="card-df">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Perlu Tindak Lanjut</h3>
                    <p class="text-sm font-normal text-gray-500 mb-4">Prospek Anda yang paling lama tidak di-update (dalam proses)</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <tbody class="divide-y divide-gray-200">
                                @if (empty($actionable_prospects))
                                    <tr>
                                        <td class="p-4 text-center text-gray-500">Kerja bagus! Tidak ada prospek yang terbengkalai.</td>
                                    </tr>
                                @else
                                    @foreach ($actionable_prospects as $prospek)
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-4">
                                            <p class="font-medium text-primary">{{ $prospek['nama_sekolah'] }}</p>
                                            <p class="text-sm text-gray-500">{{ $prospek['narahubung'] }}</p>
                                        </td>
                                        <td class="p-4 text-sm text-gray-500">Update Terakhir {{ $prospek['tgl_update'] }}</td>
                                        <td class="p-4 text-right">
                                            {{-- Arahkan ke halaman edit prospek tim marketing --}}
                                            <a href="{{ url('/tim-marketing/prospek-saya/' . $prospek['id_prospek']) }}" class="btn-outline-df btn-sm">
                                                Lihat
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Load Chart.js (CDN) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    $(document).ready(function() {
        
        const prospectChartData = {!! json_encode($prospek_chart_data) !!};
        if (prospectChartData.length > 0) {
            const labels = [];
            const data = [];
            const backgroundColors = [];
            
            const colorMap = {
                'baru': 'rgba(59, 130, 246, 0.7)',
                'dalam proses': 'rgba(245, 158, 11, 0.7)',
                'berhasil': 'rgba(16, 185, 129, 0.7)',
                'gagal': 'rgba(239, 68, 68, 0.7)',
                'batal': 'rgba(107, 114, 128, 0.7)'
            };

            prospectChartData.forEach(item => {
                let label = item.status_prospek.charAt(0).toUpperCase() + item.status_prospek.slice(1);
                labels.push(label);
                data.push(item.count);
                backgroundColors.push(colorMap[item.status_prospek] || colorMap['batal']);
            });

            const ctx1 = document.getElementById('prospectStatusChart').getContext('2d');
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Prospek',
                        data: data,
                        backgroundColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        }
    });
</script>
@endpush