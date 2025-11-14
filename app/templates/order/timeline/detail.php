{{-- templates/order/timeline/detail.php --}}
@extends('layouts.admin')

@section('title')
Detail Task
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/order') }}" class="text-primary hover:underline">Manajemen Order</a>
  <span>/</span>

  <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="text-primary hover:underline">{{ $order['nomor_order'] }}</a>

  <span>/</span>
  <span class="text-gray-600">Detail Task</span>
</div>
@endsection

@section('content')
<div class="w-full"> 
  <div class="card-df">
    <div class="p-6 space-y-4">

      <div>
        <label class="txt-title-df">Judul Task</label>
        <p class="txt-desc-df">{{ $task['judul'] }}</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="txt-title-df">Ditugaskan Ke</label>
          <p class="txt-desc-df">{{ $task['nama_user'] ?? 'N/A' }}</p>
        </div>
        <div>
          <label class="txt-title-df">Deadline</label>
          <p class="txt-desc-df">{{ date('d F Y', strtotime($task['deadline'])) }}</p>
        </div>
      </div>

      <div>
        <label class="txt-title-df">Status</label>
        <p class="txt-desc-df">{{ $task['status_timeline'] }}</p>
      </div>

      <div>
        <label class="txt-title-df">Deskripsi</label>
        <div class="prose prose-sm max-w-none text-gray-700 mt-1 p-3 border border-gray-200 rounded-md bg-gray-50 min-h-[100px]">
          @if (empty($task['deskripsi']))
          <p class="italic text-gray-500">Tidak ada deskripsi.</p>
          @else
          {{-- Menggunakan nl2br(e()) untuk keamanan dan baris baru --}}
          <p>{!! nl2br(($task['deskripsi'])) !!}</p>
          @endif
        </div>
      </div>

    </div>

    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between gap-3">

      @if(in_array(auth()['role'], ['project_officer', 'manajer_produksi']))
        <button
            type="button"
            class="btn-danger-df open-delete-modal"
            data-modal-target="#modal-konfirmasi-hapus"
            data-url="{{ url('/timeline/' . $task['id_timeline'] . '/delete') }}">
            Hapus Task
        </button>
      @else
        <div></div>
      @endif

      <div class="flex gap-3">
        <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="btn-outline-df">Kembali ke Order</a>

        @if(in_array(auth()['role'], ['project_officer', 'manajer_produksi']))
        <a href="{{ url('/timeline/' . $task['id_timeline'] . '/edit') }}" class="btn-df">Edit Task</a>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('modals')
<div
    id="modal-konfirmasi-hapus"
    class="modal-container fixed inset-0 z-40 p-4
           flex items-center justify-center 
           invisible">
    <div class="modal-overlay fixed inset-0 bg-gray-900/50 backdrop-blur-sm
                opacity-0 transition-opacity duration-300 ease-out">
    </div>

    <div class="modal-box relative z-50 w-full max-w-md rounded-lg bg-white p-6 shadow-xl 
                opacity-0 scale-95 transition-all duration-300 ease-out">
        
        <form id="form-delete-modal" action="" method="POST">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900">
                    Konfirmasi Hapus
                </h3>
                <button
                    type="button"
                    data-modal-dismiss="#modal-konfirmasi-hapus"
                    class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                    <ion-icon name="close" class="h-6 w-6"></ion-icon>
                </button>
            </div>
            <div class="mt-4">
                <p class="text-gray-600">
                    Apakah Anda yakin ingin menghapus <strong>task timeline</strong> ini? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    data-modal-dismiss="#modal-konfirmasi-hapus"
                    class="btn-outline-df">
                    Batal
                </button>
                <button
                    type="submit"
                    class="btn-danger-df">
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

{{-- ========================================================== --}}
{{--  BARU: Tambahkan @push('scripts')                         --}}
{{-- ========================================================== --}}
@push('scripts')
{{-- Asumsi helper.js (showModal, hideModal) dimuat di admin.php --}}
<script>
    $(document).ready(function() {
        // "Glue" untuk Modal Hapus
        $(document).on('click', '.open-delete-modal', function() {
            const deleteUrl = $(this).data('url');
            $('#form-delete-modal').attr('action', deleteUrl);
            showModal('#modal-konfirmasi-hapus');
        });
    });
</script>
@endpush

@push('styles')
<style>
  /* Style untuk deskripsi (nl2br) */
  .prose p {
    margin-top: 0;
    margin-bottom: 1em;
  }
</style>
@endpush