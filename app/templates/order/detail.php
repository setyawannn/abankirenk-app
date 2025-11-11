@extends('layouts.admin')

@section('title')
Detail Order: {{ $order['nomor_order'] }}
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/order') }}" class="text-primary hover:underline">Manajemen Order</a>
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
        <label for="status_order" class="txt-title-df">Status Order</label>

        @if (isset($status_options))
        <select name="status_order" id="status_order" class="input-df"
          data-id="{{ $order['id_order_produksi'] }}"
          data-original-value="{{ $order['status_order'] }}">
          @foreach($status_options as $status)
          <option value="{{ $status }}" @if($status==$order['status_order']) selected @endif>
            {{ ucwords(str_replace('_', ' ', $status)) }}
          </option>
          @endforeach
        </select>
        @else
        <p class="txt-desc-df">{!! generate_order_status_badge($order['status_order']) !!}</p>
        @endif
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

  <div class="-mb-px border-b border-gray-200">
    <div role="tablist" id="order-tabs" class="flex gap-1">

      @foreach($tabs as $index => $tab)
      <button role="tab"
        aria-selected="{{ $index == 0 ? 'true' : 'false' }}"
        class="tab-link {{ $index == 0 ? 'border-primary text-primary' : 'border-transparent text-gray-600 hover:text-gray-700 hover:border-gray-300' }} 
                          border-b-2 px-4 py-2 text-sm font-medium transition-colors"
        data-tab-id="{{ $tab['id'] }}"
        data-url="{{ $tab['url'] }}"
        data-loaded="false">
        {{ $tab['label'] }}
      </button>
      @endforeach

    </div>
  </div>

  <div id="order-tab-content" class="mt-4">
    @foreach($tabs as $index => $tab)
    <div role="tabpanel"
      id="tab-pane-{{ $tab['id'] }}"
      class="tab-pane {{ $index > 0 ? 'hidden' : '' }}">
      {{-- Skeleton loader untuk tab pertama --}}
      @if ($index == 0)
      <div class="card-df rounded-t-none skeleton-loader text-center p-8 text-gray-400">
        <ion-icon name="sync-outline" class="text-3xl animate-spin"></ion-icon>
        <p>Memuat konten...</p>
      </div>
      @endif
    </div>
    @endforeach
  </div>
</div>
@endsection

@push('modals')
{{-- Modal Konfirmasi Status (Kode Anda sudah benar) --}}
<div
  id="modal-konfirmasi-status"
  class="modal-container fixed inset-0 z-40 p-4
           flex items-center justify-center 
           invisible">
  <div class="modal-overlay fixed inset-0 bg-gray-900/50 backdrop-blur-sm
                opacity-0 transition-opacity duration-300 ease-out">
  </div>

  <div class="modal-box relative z-50 w-full max-w-md rounded-lg bg-white p-6 shadow-xl 
                opacity-0 scale-95 transition-all duration-300 ease-out">

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
{{-- Asumsi helper.js (showGlobalToast, showModal, hideModal, debounce, escapeHTML) dimuat di admin.php --}}
<script>
  $(document).ready(function() {

    // ==========================================================
    //  BAGIAN 1: LOGIKA UPDATE STATUS (Modal)
    // ==========================================================
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

    // ==========================================================
    //  BAGIAN 2: LOGIKA TABBING AJAX (Menggunakan .load())
    // ==========================================================
    const $tabs = $('#order-tabs');
    const $contentContainer = $('#order-tab-content');

    function loadTabContent($tabLink) {
      const tabId = $tabLink.data('tab-id');
      const url = $tabLink.data('url');
      const $pane = $contentContainer.find('#tab-pane-' + tabId);

      // 1. Tampilkan pane, sembunyikan yang lain
      $contentContainer.find('.tab-pane').addClass('hidden');
      $pane.removeClass('hidden');

      // 2. Tandai tab aktif (Sesuai UI baru Anda)
      $tabs.find('.tab-link')
        .removeClass('border-primary text-primary')
        .addClass('border-transparent text-gray-600 hover:text-gray-700 hover:border-gray-300')
        .attr('aria-selected', 'false');

      $tabLink
        .addClass('border-primary text-primary')
        .removeClass('border-transparent text-gray-600 hover:text-gray-700 hover:border-gray-300')
        .attr('aria-selected', 'true');

      // 3. Cek jika sudah di-load
      if ($tabLink.data('loaded') === true) {
        return;
      }

      // 4. Tampilkan skeleton (Ini sudah ada di HTML untuk tab pertama)
      if ($pane.find('.skeleton-loader').length === 0) {
        $pane.html(`<div class="card-df rounded-t-none p-6 skeleton-loader text-center p-8 text-gray-400">
                                <ion-icon name="sync-outline" class="text-3xl animate-spin"></ion-icon>
                                <p>Memuat konten...</p>
                            </div>`);
      }

      // 5. Fetch konten (HTML + <script> + <style>)
      //    Menggunakan .load() untuk eksekusi skrip yang aman
      $pane.load(url, function(response, status, xhr) {
        if (status == "error") {
          const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memuat konten.';
          $pane.html(`<div class="card-df rounded-t-none p-6 text-center p-8 text-red-500">
                                  <strong>Gagal memuat tab.</strong><br>
                                  <span class="text-sm text-gray-500">${errorMsg}</span>
                                </div>`);
          showGlobalToast('error', 'Gagal Memuat', errorMsg);
        } else {
          // Sukses: Tandai sudah di-load
          $tabLink.data('loaded', true);
        }
      });
    }

    // --- Event listener (Tidak berubah) ---
    $tabs.on('click', '.tab-link', function(e) {
      e.preventDefault();
      loadTabContent($(this));
    });

    // --- Muat tab pertama (Tidak berubah) ---
    const $firstTab = $tabs.find('.tab-link').first();
    if ($firstTab.length) {
      loadTabContent($firstTab);
    }
  });
</script>
@endpush

@push('styles')
<style>
  .timeline-card-placeholder {
    background-color: #e5e7eb;
    border: 2px dashed #9ca3af;
    height: 120px;
    border-radius: 0.375rem;
    margin-bottom: 1rem;
    width: auto;
    display: block;
  }

  .dragging-card-helper {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transform: rotate(3deg);
    cursor: grabbing !important;
  }
</style>
@endpush