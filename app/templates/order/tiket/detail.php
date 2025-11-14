@extends('layouts.admin')

@section('title')
Detail Tiket: {{ $tiket['nomor_komplain'] }}
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/order') }}" class="text-primary hover:underline">Manajemen Order</a>
  <span>/</span>
  <a href="{{ url('/order/' . $tiket['id_order_produksi'] . '/detail') }}" class="text-primary hover:underline">{{ $tiket['nomor_order'] }}</a>
  <span>/</span>
  <span class="text-gray-600">Detail Tiket</span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto space-y-6">

  <div class="card-df">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-xl font-semibold text-gray-900">Detail Tiket Komplain</h3>
      <p class="text-gray-600">Nomor: <span class="font-medium">{{ $tiket['nomor_komplain'] }}</span></p>
    </div>

    <div class="p-6 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="txt-title-df">Nama Sekolah</label>
          <p class="txt-desc-df">{{ $tiket['nama_sekolah'] }}</p>
        </div>
        <div>
          <label class="txt-title-df">Dibuat Oleh</label>
          <p class="txt-desc-df">{{ $tiket['nama_klien'] }} | {{ $tiket['formatted_created_at'] }}</p>
        </div>
      </div>

      @if (!empty($tiket['link_video']))
      <div>
        <label class="txt-title-df">Link Video Bukti</label>
        <a href="{{ $tiket['link_video'] }}" target="_blank" class="btn-outline-df w-full md:w-auto">
          Lihat Video
          <ion-icon name="open-outline" class="ml-2"></ion-icon>
        </a>
      </div>
      @endif

      <div>
        <label class="txt-title-df">Deskripsi & Bukti Foto</label>
        <div class="prose max-w-none text-gray-700 mt-1 p-3 border border-gray-200 rounded-md bg-gray-50 min-h-[150px]">
          {!! $tiket['deskripsi'] !!}
        </div>
      </div>
    </div>
  </div>

  <div class="card-df">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-xl font-semibold text-gray-900">Balasan Tiket</h3>
    </div>

    <div class="p-6">
      @if (empty($tiket['respon']))
      <p class="italic text-gray-500 text-center p-4">Belum ada balasan dari Customer Service.</p>
      @else
      <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="txt-title-df">Staf Bertanggung Jawab</label>
            <p class="txt-desc-df">{{ $tiket['nama_cs'] ?? 'N/A' }} ({{ format_role_name($tiket['role_cs'] ?? 'customer_service') }})</p>
          </div>
          <div>
            <label class="txt-title-df">Tanggal Balasan</label>
            <p class="txt-desc-df">{{ $tiket['formatted_tanggal_respon'] }}</p>
          </div>
        </div>
        <div>
          <label class="txt-title-df">Status Retur</label>
          <p class="txt-desc-df">{!! $tiket['retur_badge'] !!}</p>
        </div>
        <div>
          <label class="txt-title-df">Balasan</label>
          <div class="prose max-w-none text-gray-700 mt-1 p-3 border border-gray-200 rounded-md bg-gray-50 min-h-[150px]">
            {!! $tiket['respon'] !!}
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>

  <div class="flex justify-end">
    <a href="{{ url('/order/' . $tiket['id_order_produksi'] . '/detail') }}" class="btn-outline-df">
      Kembali ke Detail Order
    </a>
  </div>

</div>
@endsection

@push('styles')
<style>
  .prose p {
    margin-top: 0;
    margin-bottom: 1em;
  }

  .prose img {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem;
  }
</style>
@endpush