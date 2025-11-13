@extends('layouts.admin')

@section('title')
Formulir QC: {{ $order['nomor_order'] }}
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/order') }}" class="text-primary hover:underline">Manajemen Order</a>
  <span>/</span>
  <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="text-primary hover:underline">{{ $order['nomor_order'] }}</a>
  <span>/</span>
  <span class="text-gray-600">Formulir QC</span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <div class="card-df">
    <form action="{{ url('/order/' . $order['id_order_produksi'] . '/qc/store') }}" method="POST" enctype="multipart/form-data">

      <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-semibold text-gray-900">Formulir Quality Control</h3>
        <p class="text-gray-600">Order: <span class="font-medium">{{ $order['nomor_order'] }}</span> ({{ $order['nama_sekolah'] }})</p>
      </div>

      <div class="p-6 space-y-6 border-b border-gray-200">
        <h4 class="text-lg font-medium text-gray-800">1. Hasil Pengecekan Kuantitas</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label for="jumlah_sampel_diperiksa" class="label-df">Jml. Sampel Diperiksa <span class="text-red-500">*</span></label>
            <input type="number" name="jumlah_sampel_diperiksa" id="jumlah_sampel_diperiksa" class="input-df" value="0" required>
          </div>
          <div>
            <label for="jumlah_cacat" class="label-df">Jumlah Cacat Ditemukan <span class="text-red-500">*</span></label>
            <input type="number" name="jumlah_cacat" id="jumlah_cacat" class="input-df" value="0" required>
            <p class="desc-df">Jika > 0, status otomatis "Gagal Total".</p>
          </div>
          <div>
            <label for="jenis_cacat" class="label-df">Jenis Cacat Utama (jika ada)</label>
            <input type="text" name="jenis_cacat" id="jenis_cacat" class="input-df" placeholder="Misal: Jilid miring, warna pudar">
          </div>
        </div>
      </div>

      <div class="p-6 space-y-6 border-b border-gray-200">
        <h4 class="text-lg font-medium text-gray-800">2. Checklist Poin Kualitas</h4>
        <p class="desc-df -mt-4">Centang poin yang dianggap "Lolos". Tidak dicentang berarti "Gagal" (skor 0).</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
          @foreach ($bobot_qc as $key => $bobot)
          @php
          // Format 'check_cover_material' -> 'Cover Material'
          $label = ucwords(str_replace('_', ' ', str_replace('check_', '', $key)));
          @endphp
          <div class="relative flex items-start">
            <div class="flex h-6 items-center">
              <input id="{{ $key }}" name="{{ $key }}" type="checkbox" value="1" class="h-5 w-5 rounded border-gray-300 text-primary focus:ring-primary">
            </div>
            <div class="ml-3 text-sm leading-6">
              <label for="{{ $key }}" class="font-medium text-gray-900">{{ $label }}</label>
              <p class="text-gray-500">(Bobot: {{ $bobot }} poin)</p>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <div class="p-6 space-y-6">
        <h4 class="text-lg font-medium text-gray-800">3. Catatan & Bukti</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="bukti_foto" class="label-df">Lampiran Bukti Foto (jika ada)</label>
            <input type="file" name="bukti_foto" id="bukti_foto" class="input-file-df" accept="image/jpeg, image/png, image/webp">
          </div>
          <div class="col-span-2">
            <label for="catatan_qc" class="label-df">Catatan Tambahan (Opsional)</label>
            <textarea name="catatan_qc" id="catatan_qc" rows="3" class="input-df resize-none" placeholder="Catatan untuk Manajer Produksi atau PO..."></textarea>
          </div>
        </div>
      </div>

      <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
        <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="btn-outline-df">Batal</a>
        <button type="submit" class="btn-df">Simpan Hasil QC</button>
      </div>
    </form>
  </div>
</div>
@endsection