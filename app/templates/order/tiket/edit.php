@extends('layouts.admin')

@section('title')
Balas Tiket: {{ $tiket['nomor_komplain'] }}
@endsection

@php
// Helper (duplikat dari 'detail.php' untuk badge)
if (!function_exists('get_retur_badge')) {
function get_retur_badge($status) {
$baseClass = "px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full";
$colors = [
'pending' => 'bg-yellow-100 text-yellow-800',
'disetujui' => 'bg-green-100 text-green-800',
'ditolak' => 'bg-red-100 text-red-800'
];
$class = $colors[$status] ?? 'bg-gray-100 text-gray-800';
return "<span class='{$baseClass} {$class}'>" . ucfirst($status) . "</span>";
}
}
@endphp

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/order') }}" class="text-primary hover:underline">Order</a>
  <span>/</span>
  <a href="{{ url('/order/' . $tiket['id_order_produksi'] . '/detail') }}" class="text-primary hover:underline">{{ $tiket['nomor_order'] }}</a>
  <span>/</span>
  <span class="text-gray-600">Balas Tiket</span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto space-y-6">

  <div class="card-df">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-xl font-semibold text-gray-900">Detail Tiket Komplain</h3>
      <p class="text-gray-600">Nomor: <span class="font-medium">{{ $tiket['nomor_komplain'] }}</span></p>
    </div>
    <div class="p-6 space-y-4">
      {{-- (Info Read-only sama seperti di detail.php) --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="txt-title-df">Nama Sekolah</label>
          <p class="txt-desc-df">{{ $tiket['nama_sekolah'] }}</p>
        </div>
        <div>
          <label class="txt-title-df">Dibuat Oleh</label>
          <p class="txt-desc-df">{{ $tiket['nama_klien'] }} | {{ $tiket['formatted_created_at'] }}</p>
        </div>
      </div>
      @if (!empty($tiket['link_video']))
      <div>
        <label class="txt-title-df">Link Video Bukti</label>
        <a href="{{ $tiket['link_video'] }}" target="_blank" class="btn-outline-df w-full md:w-auto">Lihat Video...</a>
      </div>
      @endif
      <div>
        <label class="txt-title-df">Deskripsi & Bukti Foto</label>
        <div class="prose max-w-none text-gray-700 mt-1 p-3 border border-gray-200 rounded-md bg-gray-50 min-h-[150px]">
          {!! $tiket['deskripsi'] !!}
        </div>
      </div>
    </div>
  </div>

  <div class="card-df">
    <form action="{{ url('/tiket/' . $tiket['id_tiket'] . '/update') }}" method="POST">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-semibold text-gray-900">Form Balasan Customer Service</h3>
      </div>

      <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="col-span-2">
            <label for="status_retur" class="label-df">Status Retur <span class="text-red-500">*</span></label>
            <select name="status_retur" id="status_retur" class="input-df" required>
              @foreach ($status_retur_options as $status)
              <option value="{{ $status }}" @if($status==$tiket['status_retur']) selected @endif>
                {{ ucfirst($status) }}
              </option>
              @endforeach
            </select>
          </div>
        </div>

        <div>
          <label for="respon" class="label-df">Balasan <span class="text-red-500">*</span></label>
          <div id="ckeditor-skeleton" class="border ...">...</div>
          <textarea
            name="respon"
            id="respon"
            rows="4"
            class="input-df resize-none hidden">{{ $tiket['respon'] ?? '' }}</textarea>
        </div>
      </div>

      <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
        <a href="{{ url('/order/' . $tiket['id_order_produksi'] . '/detail') }}" class="btn-outline-df">Batal</a>
        <button type="submit" class="btn-df">Simpan Balasan</button>
      </div>
    </form>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
<script>
  $(document).ready(function() {
    const $skeleton = $('#ckeditor-skeleton');
    const $textarea = $('#respon');

    CKEDITOR.ClassicEditor.create(document.querySelector('#respon'), {
        toolbar: {
          items: ['bold', 'italic', 'link', '|', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
        },
        placeholder: 'Tulis balasan untuk klien di sini...',
        removePlugins: [
          'Comments', 'TrackChanges', 'TrackChangesData', 'TrackChangesEditing',
          'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
          'RealTimeCollaborativeEditing', 'RealTimeCollaborativeRevisionHistory',
          'RevisionHistory', 'PresenceList', 'UsersInit', 'WProofreader',
          'DocumentOutline', 'TableOfContents', 'AIAssistant', 'MultiLevelList',
          'Pagination', 'FormatPainter', 'Template', 'SlashCommand',
          'PasteFromOfficeEnhanced', 'CaseChange', 'ExportPdf', 'ExportWord', 'ImportWord',
          'SimpleUploadAdapter', 'Base64UploadAdapter'
        ]
      })
      .then(editor => {
        $skeleton.hide();
      })
      .catch(error => {
        console.error('Gagal memuat CKEditor 5:', error);
        $skeleton.hide();
        $textarea.removeClass('hidden');
      });
  });
</script>
@endpush

@push('styles')
<style>
  .ck-editor__editable {
    min-height: 200px;
  }

  .ck.ck-editor__main>.ck-editor__editable:focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 1px #4F46E5;
  }

  .ck.ck-editor__main>.ck-editor__editable,
  .ck.ck-editor__editable.ck-focused {
    border-radius: 0.375rem;
    border-color: #D1D5DB;
  }

  .prose p {
    margin-top: 0;
    margin-bottom: 1em;
  }

  .prose img {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem;
  }
</style>
@endpush