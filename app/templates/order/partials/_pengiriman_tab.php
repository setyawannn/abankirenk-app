@php
// (Data $items dan $order dikirim dari 'ajax_get_pengiriman_tab' di pengiriman_action.php)
@endphp

<div class="card-df rounded-t-none">
  <div class="p-6">

    <div class="flex justify-between items-center mb-4">
      <h4 class="text-lg font-medium">Riwayat Pengiriman</h4>

      {{-- Tombol ini hanya tampil untuk role yang berwenang --}}
      @if(in_array(auth()['role'], ['project_officer', 'manajer_produksi', 'tim_percetakan']))
      <a href="{{ url('/order/' . $order['id_order_produksi'] . '/pengiriman/create') }}" class="btn-df btn-sm">
        <ion-icon name="add"></ion-icon>
        Tambah Data Pengiriman
      </a>
      @endif
    </div>

    {{-- Daftar Riwayat Pengiriman --}}
    @if (empty($items))
    <p class="text-gray-500 text-center p-4">Belum ada data pengiriman untuk order ini.</p>
    @else
    <div class="border rounded-md divide-y divide-gray-200 border-gray-300">

      @foreach ($items as $item)
      <div class="p-4 flex justify-between items-center hover:bg-gray-50 transition-colors rounded-md">

        <div>
          <a href="{{ url('/pengiriman/' . $item['id_pengiriman'] . '/detail') }}" class="text-base font-semibold text-primary hover:underline">
            {{ $item['no_resi'] }}
          </a>
          <p class="text-sm text-gray-500">
            Ekspedisi: {{ $item['ekspedisi'] }}
          </p>
        </div>

        <div class="text-right flex-shrink-0 ml-4 space-y-1">
          <p class="text-sm text-gray-500">
            {{ date('d/m/Y', strtotime($item['tanggal_buat'])) }}
          </p>

          {{-- Tampilkan tombol "Lacak" HANYA jika URL tracking ada --}}
          @if (!empty($item['tracking_url']))
          <a href="{{ $item['tracking_url'] }}" target="_blank"
            class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200">
            Lacak
          </a>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    @endif

  </div>
</div>