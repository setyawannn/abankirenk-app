@php

if (!function_exists('generate_tiket_status_badge')) {
function generate_tiket_status_badge($status) {
$baseClass = "px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full";
$colors = [
'baru' => 'bg-blue-100 text-blue-800',
'proses' => 'bg-yellow-100 text-yellow-800',
'selesai' => 'bg-green-100 text-green-800',
'ditutup' => 'bg-gray-100 text-gray-800'
];
$class = $colors[$status] ?? $colors['ditutup'];
return "<span class='{$baseClass} {$class}'>" . ucfirst($status) . "</span>";
}
}
@endphp

<div class="card-df rounded-t-none">
  <div class="p-6">

    <div class="flex justify-between items-center mb-4">
      <h4 class="text-lg font-medium">Riwayat Tiket Komplain</h4>

      @if(auth()['role'] == 'klien' && auth()['id'] == $order['id_klien'])
      <a href="{{ url('/order/' . $order['id_order_produksi'] . '/tiket/create') }}" class="btn-df btn-sm">
        <ion-icon name="add"></ion-icon>
        Buat Tiket Baru
      </a>
      @endif
    </div>

    @if (empty($items))
    <p class="text-gray-500 text-center p-4">Belum ada tiket komplain yang dibuat untuk order ini.</p>
    @else
    <div class="border rounded-md divide-y divide-gray-200 border-gray-300">

      @foreach ($items as $item)
      @php
      $badge_html = generate_tiket_status_badge($item['status_tiket']);
      @endphp

      <a href="{{ url('/tiket/' . $item['id_tiket'] . '/detail') }}"
        class="p-4 flex justify-between items-center hover:bg-gray-50 transition-colors rounded-md">

        <div>
          <p class="text-base font-semibold text-primary hover:underline">
            {{ $item['nomor_komplain'] }}
          </p>
          <p class="text-sm text-gray-500">
            Kategori: {{ ucfirst($item['kategori']) }}
          </p>
        </div>

        <div class="text-right flex-shrink-0 ml-4 space-y-1">
          {!! $badge_html !!}
          <p class="text-sm text-gray-500 mt-1">
            Dibuat: {{ date('d/m/Y', strtotime($item['created_at'])) }}
          </p>
        </div>
      </a>
      @endforeach
    </div>
    @endif

  </div>
</div>