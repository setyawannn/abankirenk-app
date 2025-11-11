@extends('layouts.admin')

@section('title')
Tambah Task Timeline
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/order') }}" class="text-primary hover:underline">Order</a>
  <span>/</span>
  <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="text-primary hover:underline">{{ $order['nomor_order'] }}</a>
  <span>/</span>
  <span class="text-gray-600">Tambah Task</span>
</div>
@endsection

@section('content')
<div class="w-full">
  <div class="card-df">
    <form action="{{ url('/order/' . $order['id_order_produksi'] . '/timeline/store') }}" method="POST">
      <div class="p-6 space-y-4">
        <div>
          <label for="judul" class="label-df">Judul Task <span class="text-red-500">*</span></label>
          <input type="text" name="judul" id="judul" class="input-df" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="id_user" class="label-df">Ditugaskan Ke <span class="text-red-500">*</span></label>
            <select name="id_user" id="id_user" class="input-df" required>
              <option value="">Pilih Staf...</option>
              @foreach ($staff_list as $staff)
              <option value="{{ $staff['id_user'] }}">{{ $staff['nama'] }} ({{ format_role_name($staff['role']) }})</option>
              @endforeach
            </select>
          </div>
          <div>
            <label for="deadline" class="label-df">Deadline <span class="text-red-500">*</span></label>
            <input type="date" name="deadline" id="deadline" class="input-df" required>
          </div>
        </div>
        <div>
          <label for="status_timeline" class="label-df">Status <span class="text-red-500">*</span></label>
          <select name="status_timeline" id="status_timeline" class="input-df" required>
            @foreach ($status_options as $status)
            <option value="{{ $status }}">{{ $status }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="deskripsi" class="label-df">Deskripsi (Opsional)</label>
          <textarea name="deskripsi" id="deskripsi" rows="3" class="input-df resize-none"></textarea>
        </div>
      </div>

      <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
        <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="btn-outline-df">Batal</a>
        <button type="submit" class="btn-df">Simpan Task</button>
      </div>
    </form>
  </div>
</div>
@endsection