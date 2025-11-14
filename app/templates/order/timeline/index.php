@extends('layouts.admin')

@section('title')
Timeline: {{ $order['nomor_order'] }}
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/order') }}" class="text-primary hover:underline">Manajemen Order</a>
  <span>/</span>
  <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="text-primary hover:underline">{{ $order['nomor_order'] }}</a>
  <span>/</span>
  <span class="text-gray-600">Timeline Produksi</span>
</div>
@endsection

@section('content')
<div class="space-y-6">
  <div class="flex justify-between items-center">
    <div>
      <h2 class="text-2xl font-semibold">Timeline untuk Order #{{ $order['nomor_order'] }}</h2>
      <p class="text-gray-500">Sekolah: {{ $order['nama_sekolah'] }}</p>
    </div>
    @if(in_array(auth()['role'], ['project_officer', 'manajer_produksi']))
    <a href="{{ url('/order/' . $order['nomor_order'] . '/timeline/create') }}" class="btn-df">
      <ion-icon name="add"></ion-icon>
      Tambah Task
    </a>
    @endif
  </div>

  @php
  $columns = ['Ditugaskan' => [], 'Dalam Proses' => [], 'Selesai' => []];
  $badge_colors = ['Ditugaskan' => 'bg-yellow-500', 'Dalam Proses' => 'bg-orange-500', 'Selesai' => 'bg-green-600'];
  foreach ($items as $item) {
  if (isset($columns[$item['status_timeline']])) {
  $columns[$item['status_timeline']][] = $item;
  }
  }
  @endphp

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach ($columns as $status => $tasks)
    <div class="kanban-column bg-gray-100 p-4 border border-gray-200 rounded-lg h-[80vh] space-y-6 overflow-y-auto">
      <div class="{{ $badge_colors[$status] }} text-white px-4 py-1 w-fit text-sm rounded-full">
        <span>{{ $status }}</span>
      </div>

      <div class="kanban-column-content flex flex-col gap-4 min-h-[50px]" data-status="{{ $status }}">
        @if (empty($tasks))
        <p class="text-sm text-gray-500 p-4 text-center">Belum ada task.</p>
        @endif
        @foreach ($tasks as $item)
        <div class="timeline-card bg-white p-4 rounded-md space-y-2 shadow-sm"
          data-task-id="{{ $item['id_timeline'] }}">
          <div class="flex justify-between items-center">
            <h4 class="text-lg font-semibold text-gray-800">{{ $item['judul'] }}</h4>
            @if(in_array(auth()['role'], ['project_officer', 'manajer_produksi']))
            <div class="flex gap-2">
              <a href="{{ url('/timeline/' . $item['id_timeline'] . '/edit') }}" class="text-primary">
                <ion-icon name="create-outline"></ion-icon>
              </a>
              <form action="{{ url('/timeline/' . $item['id_timeline'] . '/delete') }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus task ini?');">
                <button type="submit" class="text-red-600">
                  <ion-icon name="trash-outline"></ion-icon>
                </button>
              </form>
            </div>
            @endif
          </div>
          <div class="flex flex-col gap-2">
            <div class="text-base text-gray-500 space-x-1">
              <ion-icon name="person-outline" class="text-primary"></ion-icon>
              <span>{{ $item['nama_user'] }}</span>
            </div>
            <div class="text-base text-gray-500 space-x-1">
              <ion-icon name="calendar-clear-outline" class="text-primary"></ion-icon>
              <span>{{ date('d/m/Y', strtotime($item['deadline'])) }}</span>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection