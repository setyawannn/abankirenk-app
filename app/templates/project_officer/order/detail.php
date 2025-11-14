@extends('layouts.admin')

@section('title')
{{ $order['nomor_order'] }}
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">

  <a href="{{ url('/project-officer/order') }}" class="text-primary hover:underline">Order</a>
  <span>
    /
  </span>
  <span class="text-gray-600">
    {{ $order['nomor_order'] }}
  </span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto space-y-4">

  <div class="card-df">
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="col-span-2">
        <label for="status_order" class="txt-title-df">
          Status Order
        </label>
        <select name="status_order" id="status_order" class="input-df"
          data-id="{{ $order['id_order_produksi'] }}"
          data-original-value="{{ $order['status_order'] }}">
          @foreach($status_options_po as $status)
          <option value="{{ $status }}" @if($status==$order['status_order']) selected @endif>
            {{ ucwords(str_replace('_', ' ', $status)) }}
          </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="txt-title-df">Narahubung</label>
        <p class="txt-desc-df">{{ $order['narahubung'] }} ({{ $order['no_narahubung'] }})</p>
      </div>
      <div>
        <label class="txt-title-df">Kuantitas Cetak</label>
        <p class="txt-desc-df">{{ $order['kuantitas'] }} pcs</p>
      </div>
      <div>
        <label class="txt-title-df">Detail Order Buku</label>
        <p class="txt-desc-df">{{ $order['kuantitas'] }} Pcs ({{ $order['halaman'] }} halaman)</p>
      </div>
      <div>
        <label class="txt-title-df">Deadline</label>
        <p class="txt-desc-df font-medium">{{ $order['formatted_deadline'] }}</p>
      </div>
    </div>
  </div>
</div>
@endsection

@push('modals')
<div
  id="modal-konfirmasi-status"
  class="modal-container fixed inset-0 z-40 p-4 flex items-center justify-center invisible">
  <div class="modal-overlay fixed inset-0 bg-gray-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"></div>

  <div class="modal-box relative z-50 w-full max-w-md rounded-lg bg-white p-6 shadow-xl opacity-0 scale-95 transition-all duration-300 ease-out">

    <div class="flex items-center justify-between">
      <h3 class="text-xl font-semibold text-gray-900">
        Konfirmasi Perubahan Status
      </h3>
      <button
        type="button"
        data-modal-dismiss="#modal-konfirmasi-status"
        class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
        <ion-icon name="close" class="h-6 w-6"></ion-icon>
      </button>
    </div>

    <div class="mt-4">
      <p class="text-gray-600">
        Anda yakin ingin mengubah status order ini dari
        <strong id="modal-status-old" class="font-medium text-gray-900">...</strong>
        menjadi
        <strong id="modal-status-new" class="font-medium text-gray-900">...</strong>?
      </p>
    </div>

    <div class="mt-6 flex justify-end gap-3">
      <button
        type="button"
        data-modal-dismiss="#modal-konfirmasi-status"
        class="btn-outline-df"
        id="btn-cancel-status">
        Batal
      </button>
      <button
        type="button"
        id="btn-confirm-status-update"
        class="btn-df">
        Ya, Ubah Status
      </button>
    </div>
  </div>
</div>
@endpush

@push('scripts')
<script>
  $(document).ready(function() {

    const $confirmButton = $('#btn-confirm-status-update');
    const $statusSelect = $('#status_order');

    $statusSelect.on('change', function() {
      const $select = $(this);
      const newStatus = $select.val();
      const orderId = $select.data('id');
      const originalStatus = $select.data('original-value');

      const newStatusText = $select.find('option:selected').text().trim();
      const originalStatusText = $select.find(`option[value="${originalStatus}"]`).text().trim();

      $('#modal-status-old').text(originalStatusText);
      $('#modal-status-new').text(newStatusText);

      $confirmButton.data('id_order_produksi', orderId);
      $confirmButton.data('status', newStatus);
      $confirmButton.data('original_status', originalStatus);

      showModal('#modal-konfirmasi-status');
    });

    $('#btn-cancel-status').on('click', function() {
      const originalStatus = $confirmButton.data('original_status');
      $statusSelect.val(originalStatus);
    });

    $confirmButton.on('click', function() {
      const $button = $(this);
      const orderId = $button.data('id_order_produksi');
      const newStatus = $button.data('status');
      const originalStatus = $button.data('original_status');

      hideModal('#modal-konfirmasi-status');

      $.ajax({
        url: '{{ url("/ajax/order/update-status") }}',
        type: 'POST',
        data: {
          id_order_produksi: orderId,
          status: newStatus
        },
        success: function(response) {
          $statusSelect.data('original-value', newStatus);
          showGlobalToast('success', 'Update Berhasil', response.message);
        },
        error: function(xhr) {
          const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memperbarui status.';

          showGlobalToast('error', 'Update Gagal', errorMsg + ' Status dikembalikan.');
          $statusSelect.val(originalStatus);
        }
      });
    });

  });
</script>
@endpush

@push('styles')
<style>
  .prose {
    color: #374151;
  }

  .prose p {
    margin-top: 0;
    margin-bottom: 1em;
  }
</style>
@endpush