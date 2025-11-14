@extends('layouts.admin')

@section('title')
Dashboard Klien
@endsection

@section('content')
<div class="space-y-6">

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="card-df">
      <div class="p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="txt-title-df text-primary">Pengajuan Aktif</p>
            <p class="text-3xl font-semibold text-gray-900">{{ $stats['pengajuan_aktif'] }}</p>
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
            <p class="txt-title-df text-yellow-600">Order Berjalan</p>
            <p class="text-3xl font-semibold text-gray-900">{{ $stats['order_berjalan'] }}</p>
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
            <p class="txt-title-df text-green-600">Order Selesai</p>
            <p class="text-3xl font-semibold text-gray-900">{{ $stats['order_selesai'] }}</p>
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
            <p class="txt-title-df text-red-600">Tiket Aktif</p>
            <p class="text-3xl font-semibold text-gray-900">{{ $stats['tiket_aktif'] }}</p>
          </div>
          <div class="bg-red-100 text-red-600 w-16 h-16 rounded-full flex items-center justify-center">
            <ion-icon name="alert-circle-outline" class="text-3xl"></ion-icon>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-df">
    <div class="p-6">
      <div class="flex justify-between items-center mb-4">
        <div>
          <h3 class="text-lg font-medium text-gray-900">Order Saya</h3>
          <p class="text-sm font-normal text-gray-500">5 order terakhir Anda</p>
        </div>
        <a href="{{ url('/klien/pengajuan-order/create') }}" class="btn-df btn-sm">
          Buat Pengajuan Baru
        </a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full">
          <tbody class="divide-y divide-gray-200">
            @if (empty($orders))
            <tr>
              <td class="p-4 text-center text-gray-500">Anda belum memiliki order aktif.</td>
            </tr>
            @else
            @foreach ($orders as $order)
            @php
            $badge = generate_order_status_badge($order['status_order']);
            @endphp
            <tr class="hover:bg-gray-50">
              <td class="p-4">
                <p class="font-medium text-primary">{{ $order['nomor_order'] }}</p>
                <p class="text-sm text-gray-500">{{ $order['nama_sekolah'] }}</p>
              </td>
              <td class="p-4">{!! $badge !!}</td>
              <td class="p-4 text-sm text-gray-500">Diperbarui {{ $order['tgl_update'] }}</td>
              <td class="p-4 text-right">
                <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="btn-outline-df btn-sm">
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