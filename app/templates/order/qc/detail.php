@extends('layouts.admin')

@section('title')
Detail QC: {{ $qc['batch_number'] }}
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/order') }}" class="text-primary hover:underline">Manajemen Order</a>
  <span>/</span>
  <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="text-primary hover:underline">{{ $order['nomor_order'] }}</a>
  <span>/</span>
  <span class="text-gray-600">Detail QC</span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <div class="md:col-span-2 space-y-6">
      <div class="card-df">
        <div class="p-6">
          <h3 class="text-xl font-semibold text-gray-900 mb-4">Hasil Quality Control</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="txt-title-df">Status</label>
              @php
              $badge_class = 'bg-gray-100 text-gray-800';
              if ($qc['status_kelolosan'] == 'Lolos') {
              $badge_class = 'bg-green-100 text-green-800';
              } elseif ($qc['status_kelolosan'] == 'Gagal Total') {
              $badge_class = 'bg-red-100 text-red-800';
              } elseif ($qc['status_kelolosan'] == 'Gagal Sebagian') {
              $badge_class = 'bg-yellow-100 text-yellow-800';
              }
              @endphp
              <p class="txt-desc-df"><span class="px-3 py-1 text-sm font-semibold rounded-full {{ $badge_class }}">{{ $qc['status_kelolosan'] }}</span></p>
            </div>
            <div>
              <label class="txt-title-df">Persentase Lolos</label>
              <p class="txt-desc-df font-semibold text-lg">{{ number_format($qc['persentase_lolos'], 2) }}%</p>
            </div>
            <div>
              <label class="txt-title-df">Jumlah Cacat</label>
              <p class="txt-desc-df font-semibold text-lg {{ $qc['jumlah_cacat'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                {{ $qc['jumlah_cacat'] }}
              </p>
            </div>
          </div>
          <div class="mt-4">
            <label class="txt-title-df">Jenis Cacat Utama</label>
            <p class="txt-desc-df">{{ $qc['jenis_cacat'] ?? '-' }}</p>
          </div>
          <div class="mt-4">
            <label class="txt-title-df">Catatan Pemeriksa</label>
            <div class="prose prose-sm max-w-none text-gray-700 mt-1 p-3 border border-gray-200 rounded-md bg-gray-50 min-h-[100px]">
              @if (empty($qc['catatan_qc']))
              <p class="italic text-gray-500">Tidak ada catatan.</p>
              @else
              <p>{!! nl2br($qc['catatan_qc']) !!}</p>
              @endif
            </div>
          </div>
        </div>
      </div>

      <div class="card-df">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Poin Checklist</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            @foreach ($bobot_qc as $key => $bobot)
            @php
            $label = ucwords(str_replace('_', ' ', str_replace('check_', '', $key)));
            $is_checked = ($qc[$key] ?? 0) == 1;
            @endphp
            <div class="flex items-center gap-3">
              @if ($is_checked)
              <ion-icon name="checkmark-circle" class="w-6 h-6 text-green-500"></ion-icon>
              @else
              <ion-icon name="close-circle" class="w-6 h-6 text-red-500"></ion-icon>
              @endif
              <span class="text-gray-700">{{ $label }}</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>

    </div>

    <div class="md:col-span-1 space-y-6">
      <div class="card-df">
        <div class="p-6 space-y-4">
          <div>
            <label class="txt-title-df">Batch Number</label>
            <p class="txt-desc-df">{{ $qc['batch_number'] }}</p>
          </div>
          <div>
            <label class="txt-title-df">Diperiksa Oleh</label>
            <p class="txt-desc-df">{{ $qc['nama_pemeriksa'] ?? 'N/A' }}</p>
          </div>
          <div>
            <label class="txt-title-df">Tanggal Pemeriksaan</label>
            <p class="txt-desc-df">{{ date('d F Y, H:i', strtotime($qc['tanggal_qc'])) }}</p>
          </div>
          <div>
            <label class="txt-title-df">Jumlah Sampel</label>
            <p class="txt-desc-df">{{ $qc['jumlah_sampel_diperiksa'] }} pcs</p>
          </div>
        </div>
      </div>
      <div class="card-df">
        <div class="p-6">
          <label class="txt-title-df">Bukti Foto Cacat (jika ada)</label>
          @if (empty($qc['bukti_foto']))
          <p class="txt-desc-df italic">Tidak ada bukti foto.</p>
          @else
          <img src="{{ url($qc['bukti_foto']) }}" alt="Bukti QC" class="w-full rounded-md border border-gray-200 mt-2">
          @endif
        </div>
      </div>
      <div class="flex justify-end">
        <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="btn-outline-df">
          Kembali ke Order
        </a>
      </div>
    </div>

  </div>
</div>
@endsection

@push('styles')
<style>
  .prose p {
    margin-top: 0;
    margin-bottom: 1em;
  }
</style>
@endpush