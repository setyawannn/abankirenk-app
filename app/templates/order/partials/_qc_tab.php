@php
// (Data $items, $order, dan $is_lolos dikirim dari 'ajax_get_qc_tab' di qc_action.php)
@endphp

<div class="card-df rounded-t-none">
  <div class="p-6">

    <div class="flex justify-between items-center mb-4">
      <h4 class="text-lg font-medium">Quality Control (QC)</h4>

      @if(in_array(auth()['role'], ['project_officer', 'manajer_produksi', 'tim_percetakan']))
      @if ($is_lolos)
      <span class="btn-df btn-sm bg-green-100 text-green-700 border-none cursor-not-allowed">
        <ion-icon name="checkmark-circle-outline" class="mr-2"></ion-icon>
        Sudah Lolos QC
      </span>
      @else
      <a href="{{ url('/order/' . $order['id_order_produksi'] . '/qc/create') }}" class="btn-df btn-sm">
        <ion-icon name="add"></ion-icon>
        QC Baru
      </a>
      @endif
      @endif
    </div>

    @if (empty($items))
    <p class="text-gray-500 text-center p-4">Belum ada riwayat QC yang dilakukan untuk order ini.</p>
    @else
    <div class="border rounded-md divide-y divide-gray-200 border-gray-300">

      @foreach ($items as $item)
      @php
      // Tentukan style badge berdasarkan status
      $badge_class = 'bg-gray-100 text-gray-800'; // Default
      if ($item['status_kelolosan'] == 'Lolos') {
      $badge_class = 'bg-green-100 text-green-800';
      } elseif ($item['status_kelolosan'] == 'Gagal Total') {
      $badge_class = 'bg-red-100 text-red-800';
      } elseif ($item['status_kelolosan'] == 'Gagal Sebagian') {
      $badge_class = 'bg-yellow-100 text-yellow-800';
      }
      @endphp

      {{-- Setiap item adalah link ke halaman detail QC --}}
      <a href="{{ url('/qc/' . $item['id_qc'] . '/detail') }}"
        class="p-4 flex justify-between items-center hover:bg-gray-50 transition-colors rounded-md">

        <div>
          <p class="text-base font-semibold text-primary hover:underline">
            {{ $item['batch_number'] }}
          </p>
          <p class="text-sm text-gray-500">
            Diperiksa oleh: {{ $item['nama_pemeriksa'] ?? 'N/A' }}
          </p>
        </div>

        <div class="text-right flex-shrink-0 ml-4">
          <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badge_class }}">
            {{ $item['status_kelolosan'] }}
          </span>
          <p class="text-sm text-gray-500 mt-1">
            {{ date('d/m/Y H:i', strtotime($item['tanggal_qc'])) }}
          </p>
        </div>
      </a>
      @endforeach
    </div>
    @endif

  </div>
</div>