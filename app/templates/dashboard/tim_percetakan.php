@extends('layouts.admin')

@section('title')
Dashboard Tim Percetakan
@endsection

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card-df">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="txt-title-df text-primary">Tugas Baru Saya</p>
                        <p class="text-3xl font-semibold text-gray-900">{{ $stats['tugas_baru'] }}</p>
                    </div>
                    <div class="bg-primary-100 text-primary w-16 h-16 rounded-full flex items-center justify-center">
                        <ion-icon name="clipboard-outline" class="text-3xl"></ion-icon>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-df">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="txt-title-df text-yellow-600">Tugas Dikerjakan</p>
                        <p class="text-3xl font-semibold text-gray-900">{{ $stats['tugas_dikerjakan'] }}</p>
                    </div>
                    <div class="bg-yellow-100 text-yellow-600 w-16 h-16 rounded-full flex items-center justify-center">
                        <ion-icon name="sync-outline" class="text-3xl"></ion-icon>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-df">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="txt-title-df text-red-600">Tugas Mendesak</p>
                        <p class="text-3xl font-semibold text-gray-900">{{ $stats['tugas_mendesak'] }}</p>
                    </div>
                    <div class="bg-red-100 text-red-600 w-16 h-16 rounded-full flex items-center justify-center">
                        <ion-icon name="alert-circle-outline" class="text-3xl"></ion-icon>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-df">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="txt-title-df text-orange-600">Perlu Rework (QC)</p>
                        <p class="text-3xl font-semibold text-gray-900">{{ $stats['qc_gagal_terbaru'] }}</p>
                    </div>
                    <div class="bg-orange-100 text-orange-600 w-16 h-16 rounded-full flex items-center justify-center">
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Beban Tugas Saya</h3>
                    <div class="h-80 w-full">
                        <canvas id="taskStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-2">
            <div class="card-df">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Tugas Aktif Saya</h3>
                    <p class="text-sm font-normal text-gray-500 mb-4">Diurutkan berdasarkan deadline terdekat</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <tbody class="divide-y divide-gray-200">
                                @if (empty($active_tasks))
                                    <tr>
                                        <td class="p-4 text-center text-gray-500">Kerja bagus! Tidak ada tugas aktif.</td>
                                    </tr>
                                @else
                                    @foreach ($active_tasks as $task)
                                    @php
                                        $status_badge = $task['status_timeline'] == 'Ditugaskan' 
                                            ? 'bg-yellow-100 text-yellow-800' 
                                            : 'bg-orange-100 text-orange-800';
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-4">
                                            <a href="{{ url('/timeline/' . $task['id_timeline'] . '/detail') }}" class="font-medium text-primary hover:underline">{{ $task['judul'] }}</a>
                                            <p class="text-sm text-gray-500">{{ $task['nomor_order'] }} ({{ $task['nama_sekolah'] }})</p>
                                        </td>
                                        <td class="p-4">
                                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status_badge }}">
                                                {{ $task['status_timeline'] }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-sm text-red-600 font-medium">
                                            {{ date('d M Y', strtotime($task['deadline'])) }}
                                        </td>
                                        <td class="p-4 text-right">
                                            <a href="{{ url('/timeline/' . $task['id_timeline'] . '/detail') }}" class="btn-outline-df btn-sm">
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
    
    <div class="card-df">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">Info Rework </h3>
            <p class="text-sm font-normal text-gray-500 mb-4">Daftar order terbaru yang gagal QC</p>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <tbody class="divide-y divide-gray-200">
                        @if (empty($failed_qc_list))
                            <tr>
                                <td class="p-4 text-center text-gray-500">Tidak ada order yang gagal QC.</td>
                            </tr>
                        @else
                            @foreach ($failed_qc_list as $qc)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4">
                                    <p class="font-medium text-gray-900">{{ $qc['nomor_order'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $qc['nama_sekolah'] }}</p>
                                </td>
                                <td class="p-4 text-sm font-medium text-red-600">{{ $qc['batch_number'] }}</td>
                                <td class="p-4 text-sm text-gray-500">{{ $qc['status_kelolosan'] }}</td>
                                <td class="p-4 text-right">
                                    <a href="{{ url('/qc/' . $qc['id_qc'] . '/detail') }}" class="btn-outline-df btn-sm">
                                        Lihat Detail
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
@endsection

@push('scripts')
{{-- Load Chart.js (CDN) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    $(document).ready(function() {
        
        // ==========================================================
        //  Chart 1: Status Tugas (Doughnut)
        // ==========================================================
        const taskChartData = {!! json_encode($task_chart_data) !!};
        if (taskChartData.length > 0) {
            const labels = [];
            const data = [];
            const backgroundColors = [];
            
            const colorMap = {
                'Ditugaskan': 'rgba(245, 158, 11, 0.7)', // Amber
                'Dalam Proses': 'rgba(234, 88, 12, 0.7)', // Orange
            };

            taskChartData.forEach(item => {
                labels.push(item.status_timeline);
                data.push(item.count);
                backgroundColors.push(colorMap[item.status_timeline]);
            });

            const ctx1 = document.getElementById('taskStatusChart').getContext('2d');
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Tugas',
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