{{-- Ini adalah file partial --}}
<div class="card-df rounded-t-none"> {{-- Tambahkan card-df di sini --}}
  <div class="p-6"> {{-- Tambahkan padding --}}

    <div class="flex justify-between items-center mb-4">
      <h4 class="text-lg font-medium">Timeline Produksi</h4>
      @if(in_array(auth()['role'], ['project_officer', 'manajer_produksi']))
      <button class="btn-df btn-sm">
        <ion-icon name="add"></ion-icon>
        Tambah Task
      </button>
      @endif
    </div>

    <div class="space-y-4">
      @if (empty($items))
      <p class="text-gray-500 text-center p-4">Belum ada timeline yang dibuat.</p>
      @else
      @foreach($items as $item)
      <div class="p-4 border rounded-md shadow-sm bg-gray-50">
        <p class="font-medium text-gray-900">{{ $item['judul'] }}</p>
        <p class="text-sm text-gray-500">Ditugaskan ke: {{ $item['user'] }}</p>
        <p class="text-sm text-gray-500">Deadline: {{ $item['deadline'] }}</p>
      </div>
      @endforeach
      @endif
    </div>

  </div>
</div>