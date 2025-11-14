@extends('layouts.admin')

@section('title')
Dashboard Project Officer
@endsection

@section('content')
<div class="space-y-6">

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="card-df">
      <div class="p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="txt-title-df text-primary">Pengajuan Baru</p>
            <p class="text-3xl font-semibold text-gray-900">{{ $stats['pengajuan_baru'] }}</p>
          </div>
          <div class="bg-primary-100 text-primary w-16 h-16 rounded-full flex items-center justify-center">
            <ion-icon name="mail-unread-outline" class="text-3xl"></ion-icon>
          </div>
        </div>
      </div>
    </div>
    <div class="card-df">
      <div class="p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="txt-title-df text-yellow-600">Order Aktif</p>
            <p class="text-3xl font-semibold text-gray-900">{{ $stats['order_aktif'] }}</p>
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
            <p class="txt-title-df text-red-600">Timeline Mendesak</p>
            <p class="text-3xl font-semibold text-gray-900">{{ $stats['timeline_mendesak'] }}</p>
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
            <p class="txt-title-df text-green-600">Selesai (Bln Ini)</p>
            <p class="text-3xl font-semibold text-gray-900">{{ $stats['order_selesai_bulan_ini'] }}</p>
          </div>
          <div class="bg-green-100 text-green-600 w-16 h-16 rounded-full flex items-center justify-center">
            <ion-icon name="checkmark-done-outline" class="text-3xl"></ion-icon>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
      <div class="card-df">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Status Order Keseluruhan</h3>
          <div class="h-80 w-full">
            <canvas id="orderStatusChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="lg:col-span-1">
      <div class="card-df">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900">Tugas Mendesak</h3>
          <p class="text-sm font-normal text-gray-500 mb-4">Deadline mendatang dalam 7 hari</p>

          <div class="space-y-4">
            @if (empty($urgent_tasks))
            <p class="text-gray-500 text-center p-4">Tidak ada tugas mendesak.</p>
            @else
            @foreach ($urgent_tasks as $task)
            <a href="{{ url('/timeline/' . $task['id_timeline'] . '/detail') }}" class="block p-3 rounded-md border border-gray-200 hover:bg-gray-50 space-y-1">
              <p class="font-semibold text-primary truncate">{{ $task['judul'] }}</p>
              <p class="text-sm text-gray-500">{{ $task['nomor_order'] }}</p>
              <p class="text-sm font-medium text-red-500 flex items-center gap-2"> <ion-icon name="time-outline" class="text-xl"></ion-icon> {{ date('d M Y', strtotime($task['deadline'])) }}</p>
            </a>
            @endforeach
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-df">
    <div class="p-6">
      <h3 class="text-lg font-medium text-gray-900">Pengajuan Order</h3>
      <p class="text-sm font-normal text-gray-500 mb-4">Menunggu response persetujuan</p>
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <tbody class="divide-y divide-gray-200">
            @if (empty($pending_pengajuan))
            <tr>
              <td class="p-4 text-center text-gray-500">Tidak ada pengajuan baru.</td>
            </tr>
            @else
            @foreach ($pending_pengajuan as $pengajuan)
            <tr class="hover:bg-gray-50 border border-gray-300 rounded-lg flex justify-between items-center">
              <td class="p-4">
                <p class="font-medium text-primary">{{ $pengajuan['nama_sekolah'] }}</p>
                <p class="text-sm text-gray-500">{{ $pengajuan['narahubung'] }} ({{ $pengajuan['no_narahubung'] }})</p>
              </td>
              <td class="p-4 text-sm text-gray-500">{{ $pengajuan['tgl_pengajuan'] }}</td>
              <td class="p-4 text-right">
                <a href="{{ url('/project-officer/pengajuan-order/' . $pengajuan['id_pengajuan'] . '/edit') }}" class="btn-outline-df btn-sm">
                  Tindak Lanjut
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  $(document).ready(function() {

    const chartData = {!! json_encode($order_chart_data) !!};

    if (chartData.length > 0) {
      const labels = [];
      const data = [];
      const backgroundColors = [];

      const colorMap = {
        'baru': 'rgba(59, 130, 246, 0.7)', // Blue
        'proses': 'rgba(245, 158, 11, 0.7)', // Amber
        'selesai': 'rgba(16, 185, 129, 0.7)', // Emerald
        'batal': 'rgba(107, 114, 128, 0.7)' // Gray
      };
      const borderColorMap = {
        'baru': 'rgb(59, 130, 246)',
        'proses': 'rgb(245, 158, 11)',
        'selesai': 'rgb(16, 185, 129)',
        'batal': 'rgb(107, 114, 128)'
      };

      chartData.forEach(item => {
        let label = item.status_order.charAt(0).toUpperCase() + item.status_order.slice(1);
        labels.push(label);
        data.push(item.count);
        backgroundColors.push(colorMap[item.status_order] || colorMap['batal']);
      });

      // Inisialisasi Chart
      const ctx = document.getElementById('orderStatusChart').getContext('2d');
      new Chart(ctx, {
        type: 'pie', // Tipe chart (pie, bar, line)
        data: {
          labels: labels,
          datasets: [{
            label: 'Jumlah Order',
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
              position: 'top',
            }
          }
        }
      });
    }
  });
</script>
@endpush