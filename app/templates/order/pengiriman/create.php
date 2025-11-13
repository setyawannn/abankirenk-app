@extends('layouts.admin')

@section('title')
Input Data Pengiriman
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/order') }}" class="text-primary hover:underline">Manajemen Order</a>
  <span>/</span>
  <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="text-primary hover:underline">{{ $order['nomor_order'] }}</a>
  <span>/</span>
  <span class="text-gray-600">Input Pengiriman</span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <div class="card-df">
    <form action="{{ url('/order/' . $order['id_order_produksi'] . '/pengiriman/store') }}" method="POST">

      <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-semibold text-gray-900">Formulir Pengiriman</h3>
        <p class="text-gray-600">Order: <span class="font-medium">{{ $order['nomor_order'] }}</span> ({{ $order['nama_sekolah'] }})</p>
      </div>

      <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="ekspedisi" class="label-df">Nama Ekspedisi <span class="text-red-500">*</span></label>
            <input type="text" name="ekspedisi" id="ekspedisi" class="input-df" placeholder="Contoh: JNE Express, Sicepat, J&T" required>
          </div>
          <div>
            <label for="tanggal_buat" class="label-df">Tanggal Kirim <span class="text-red-500">*</span></label>
            <input type="date" name="tanggal_buat" id="tanggal_buat" class="input-df" value="{{ date('Y-m-d') }}" required>
          </div>
        </div>
        <div>
          <label for="no_resi" class="label-df">Nomor Resi / AWB <span class="text-red-500">*</span></label>
          <input type="text" name="no_resi" id="no_resi" class="input-df" placeholder="Masukkan nomor resi..." required>
        </div>
        <div>
          <p class="desc-df">Link pelacakan (jika tersedia) akan dibuat secara otomatis.</p>
        </div>
      </div>

      <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
        <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="btn-outline-df">Batal</a>
        <button type="submit" class="btn-df">Simpan Data Pengiriman</button>
      </div>
    </form>
  </div>
</div>
@endsection