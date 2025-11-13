@extends('layouts.admin')

@section('title')
Detail Pengiriman: {{ $pengiriman['no_resi'] }}
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/order') }}" class="text-primary hover:underline">Manajemen Order</a>
  <span>/</span>
  <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="text-primary hover:underline">{{ $order['nomor_order'] }}</a>
  <span>/</span>
  <span class="text-gray-600">Detail Pengiriman</span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto"> {{-- Tidak perlu menambahkan max-w-2xl --}}
  <div class="card-df">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-xl font-semibold text-gray-900">Detail Pengiriman</h3>
      <p class="text-gray-600">Order: <span class="font-medium">{{ $order['nomor_order'] }}</span></p>
    </div>

    <div class="p-6 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="txt-title-df">Ekspedisi</label>
          <p class="txt-desc-df">{{ $pengiriman['ekspedisi'] }}</p>
        </div>
        <div>
          <label class="txt-title-df">Nomor Resi / AWB</label>
          <p class="txt-desc-df font-semibold">{{ $pengiriman['no_resi'] }}</p>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="txt-title-df">Tanggal Kirim</label>
          <p class="txt-desc-df">{{ date('d F Y', strtotime($pengiriman['tanggal_buat'])) }}</p>
        </div>
        <div>
          <label class="txt-title-df">Tanggal Sampai</label>
          <p class="txt-desc-df">{{ $pengiriman['tanggal_sampai'] ? date('d F Y', strtotime($pengiriman['tanggal_sampai'])) : 'Belum sampai' }}</p>
        </div>
      </div>
      <div>
        <label class="txt-title-df">Diinput Oleh</label>
        <p class="txt-desc-df">{{ $pengiriman['nama_user_input'] ?? 'N/A' }}</p>
      </div>

      @if (!empty($pengiriman['tracking_url']))
      <div>
        <label class="txt-title-df">Lacak Paket</label>
        <a href="{{ $pengiriman['tracking_url'] }}" target="_blank" class="btn-df w-full md:w-auto">
          Lacak di Website Ekspedisi
          <ion-icon name="open-outline" class="ml-2"></ion-icon>
        </a>
      </div>
      @endif
    </div>

    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between gap-3">
      @if(auth()['role'] == 'manajer_produksi')
      <button
        type="button"
        class="btn-danger-df open-delete-modal"
        data-modal-target="#modal-konfirmasi-hapus"
        data-url="{{ url('/pengiriman/' . $pengiriman['id_pengiriman'] . '/delete') }}">
        Hapus Data
      </button>
      @else
      <div></div> {{-- Placeholder agar justify-between berfungsi --}}
      @endif

      <div class="flex gap-3">
        <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="btn-outline-df">Kembali ke Order</a>

        @if(auth()['role'] == 'klien' && auth()['id'] == $order['id_klien'] && empty($pengiriman['tanggal_sampai']))
        <form action="{{ url('/pengiriman/' . $pengiriman['id_pengiriman'] . '/konfirmasi') }}" method="POST">
          <button type="button" class="btn-df" data-modal-target="#modal-konfirmasi-diterima">
            <ion-icon name="checkmark-done-outline" class="mr-2"></ion-Icon>
            Konfirmasi Paket Diterima
          </button>
        </form>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('modals')
<div id="modal-konfirmasi-hapus" class="modal-container fixed inset-0 z-40 p-4 flex items-center justify-center invisible">
  <div class="modal-overlay fixed inset-0 bg-gray-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"></div>
  <div class="modal-box relative z-50 w-full max-w-md rounded-lg bg-white p-6 shadow-xl opacity-0 scale-95 transition-all duration-300 ease-out">
    <form id="form-delete-modal" action="" method="POST">
      <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold text-gray-900">Konfirmasi Hapus</h3>
        <button type="button" data-modal-dismiss="#modal-konfirmasi-hapus" class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"><ion-icon name="close" class="h-6 w-6"></ion-icon></button>
      </div>
      <div class="mt-4">
        <p class="text-gray-600">
          Anda yakin ingin menghapus data pengiriman (Resi: <strong>{{ $pengiriman['no_resi'] }}</strong>) ini?
        </p>
      </div>
      <div class="mt-6 flex justify-end gap-3">
        <button type="button" data-modal-dismiss="#modal-konfirmasi-hapus" class="btn-outline-df">Batal</button>
        <button type="submit" class="btn-danger-df">Ya, Hapus</button>
      </div>
    </form>
  </div>
</div>

<div id="modal-konfirmasi-diterima" class="modal-container fixed inset-0 z-40 p-4 flex items-center justify-center invisible">
  <div class="modal-overlay fixed inset-0 bg-gray-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"></div>
  <div class="modal-box relative z-50 w-full max-w-md rounded-lg bg-white p-6 shadow-xl opacity-0 scale-95 transition-all duration-300 ease-out">

    <form id="form-konfirmasi-diterima" action="{{ url('/pengiriman/' . $pengiriman['id_pengiriman'] . '/konfirmasi') }}" method="POST">
      <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold text-gray-900">Konfirmasi Penerimaan</h3>
        <button type="button" data-modal-dismiss="#modal-konfirmasi-diterima" class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"><ion-icon name="close" class="h-6 w-6"></ion-icon></button>
      </div>
      <div class="mt-4">
        <p class="text-gray-600">
          Apakah Anda yakin ingin mengonfirmasi bahwa paket (Resi: <strong>{{ $pengiriman['no_resi'] }}</strong>) telah diterima?
        </p>
        <p class="text-sm text-gray-500 mt-2">Tindakan ini akan menandai order sebagai telah sampai pada tanggal hari ini.</p>
      </div>
      <div class="mt-6 flex justify-end gap-3">
        <button type="button" data-modal-dismiss="#modal-konfirmasi-diterima" class="btn-outline-df">Batal</button>
        <button type="submit" class="btn-df">Ya, Konfirmasi</button>
      </div>
    </form>
  </div>
</div>
@endpush

@push('scripts')
<script>
  $(document).ready(function() {
    $('.open-delete-modal').on('click', function() {
      const deleteUrl = $(this).data('url');

      $('#form-delete-modal').attr('action', deleteUrl);

      showModal('#modal-konfirmasi-hapus');
    });
  });
</script>
@endpush

@push('styles')
<style>
  .prose p {
    margin-top: 0;
    margin-bottom: 1em;
  }
</style>
@endpush