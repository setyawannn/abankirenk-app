@extends('layouts.admin')

@section('title')
Dashboard Manajer Marketing
@endsection

@section('content')
<div class="space-y-6">

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="card-df">
      <div class="p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="txt-title-df text-primary">Prospek Baru</p>
            <p class="text-3xl font-semibold text-gray-900">{{ $stats['prospek_baru'] }}</p>
          </div>
          <div class="bg-primary-100 text-primary w-16 h-16 rounded-full flex items-center justify-center">
            <ion-icon name="person-add-outline" class="text-3xl"></ion-icon>
          </div>
        </div>
      </div>
    </div>
    <div class="card-df">
      <div class="p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="txt-title-df text-yellow-600">Prospek Aktif</p>
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
          <h3 class="text-lg font-medium text-gray-900 mb-4">Status Prospek</h3>
          <div class="h-80 w-full">
            <canvas id="prospectStatusChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="lg:col-span-2">
      <div class="card-df">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Performa Staf (Total Prospek Ditangani)</h3>
          <div class="h-80 w-full">
            <canvas id="staffPerformanceChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-df">
    <div class="p-6">
      <h3 class="text-lg font-medium text-gray-900">Konversi Terbaru</h3>
      <p class="text-sm font-normal text-gray-500 mb-4">Prospek yang baru saja berhasil dikonversi</p>
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <tbody class="divide-y divide-gray-200">
            @if (empty($recent_success))
            <tr>
              <td class="p-4 text-center text-gray-500">Belum ada konversi berhasil baru-baru ini.</td>
            </tr>
            @else
            @foreach ($recent_success as $prospek)
            <tr class="hover:bg-gray-50">
              <td class="p-4">
                <p class="font-medium text-primary">{{ $prospek['nama_sekolah'] }}</p>
                <p class="text-sm text-gray-500">{{ $prospek['narahubung'] }}</p>
              </td>
              <td class="p-4 text-sm text-gray-500">Dikonversi {{ $prospek['tgl_konversi'] }}</td>
              <td class="p-4 text-right">
                <a href="{{ url('/manajer-marketing/manajemen-prospek/' . $prospek['id_prospek'] . '/edit') }}" class="btn-outline-df btn-sm">
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
@endsection

@push('scripts')
{{-- Load Chart.js (CDN) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  $(document).ready(function() {

    // ==========================================================
    //  Chart 1: Status Prospek (Doughnut)
    // ==========================================================
    const prospectChartData = {!! json_encode($prospek_chart_data) !!};
    if (prospectChartData.length > 0) {
      const labels = [];
      const data = [];
      const backgroundColors = [];

      const colorMap = {
        'baru': 'rgba(59, 130, 246, 0.7)', // Blue
        'dalam proses': 'rgba(245, 158, 11, 0.7)', // Amber
        'berhasil': 'rgba(16, 185, 129, 0.7)', // Emerald
        'gagal': 'rgba(239, 68, 68, 0.7)', // Red
        'batal': 'rgba(107, 114, 128, 0.7)' // Gray
      };

      prospectChartData.forEach(item => {
        let label = item.status_prospek.charAt(0).toUpperCase() + item.status_prospek.slice(1);
        labels.push(label);
        data.push(item.count);
        backgroundColors.push(colorMap[item.status_prospek] || colorMap['batal']);
      });

      const ctx1 = document.getElementById('prospectStatusChart').getContext('2d');
      new Chart(ctx1, {
        type: 'doughnut', // Tipe chart
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
            legend: {
              position: 'top'
            }
          }
        }
      });
    }

    // ==========================================================
    //  Chart 2: Performa Staf (Bar)
    // ==========================================================
    const staffChartData = {!! json_encode($staff_chart_data) !!};
    if (staffChartData.length > 0) {
      const labels_staff = [];
      const data_staff = [];

      staffChartData.forEach(item => {
        labels_staff.push(item.nama);
        data_staff.push(item.total_ditangani);
      });

      const ctx2 = document.getElementById('staffPerformanceChart').getContext('2d');
      new Chart(ctx2, {
        type: 'bar', // Tipe chart
        data: {
          labels: labels_staff,
          datasets: [{
            label: 'Total Prospek Ditangani',
            data: data_staff,
            backgroundColor: 'rgba(59, 130, 246, 0.7)', // Blue
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1
          }]
        },
        options: {
          indexAxis: 'y', // Membuat bar chart horizontal
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            } // Sembunyikan legenda
          },
          scales: {
            x: {
              beginAtZero: true
            }
          }
        }
      });
    }
  });
</script>
@endpush