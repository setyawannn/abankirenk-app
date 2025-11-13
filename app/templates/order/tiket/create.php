@extends('layouts.admin')

@section('title')
Buat Tiket Komplain
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/order') }}" class="text-primary hover:underline">Manajemen Order</a>
  <span>/</span>
  <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="text-primary hover:underline">{{ $order['nomor_order'] }}</a>
  <span>/</span>
  <span class="text-gray-600">Buat Tiket</span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <form action="{{ url('/order/' . $order['id_order_produksi'] . '/tiket/store') }}" method="POST">
    <div class="card-df">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-semibold text-gray-900">Buat Tiket Komplain Baru</h3>
        <p class="text-gray-600">Untuk Order: <span class="font-medium">{{ $order['nomor_order'] }}</span> ({{ $order['nama_sekolah'] }})</p>
        {{-- Input tersembunyi untuk ID Order --}}
        <input type="hidden" name="id_order_produksi" value="{{ $order['id_order_produksi'] }}">
      </div>

      <div class="p-6 space-y-4">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="kategori" class="label-df">Kategori <span class="text-red-500">*</span></label>
            <select name="kategori" id="kategori" class="input-df" required>
              <option value="">Pilih Kategori...</option>
              @foreach ($kategori_options as $kategori)
              <option value="{{ $kategori }}">{{ ucfirst($kategori) }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label for="link_video" class="label-df">Link Video (Opsional)</label>
            <input type="url" name="link_video" id="link_video" class="input-df" placeholder="https://youtube.com/...">
          </div>
        </div>

        <div>
          <label for="deskripsi" class="label-df">Deskripsi & Bukti Foto <span class="text-red-500">*</span></label>
          <p class="desc-df mb-2">Jelaskan masalah Anda. Anda bisa meng-upload/paste bukti foto langsung di editor di bawah ini.</p>

          <div id="ckeditor-skeleton" class="border border-gray-300 rounded-md shadow-sm">
            <div class="animate-pulse h-12 bg-gray-200 border-b border-gray-300 rounded-t-md"></div>
            <div class="animate-pulse p-4 space-y-3" style="min-height: 250px;">
              <div class="h-4 bg-gray-200 rounded w-full"></div>
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            </div>
          </div>
          <textarea
            name="deskripsi"
            id="deskripsi"
            rows="4"
            class="input-df resize-none hidden"
            placeholder="Jelaskan keluhan, pertanyaan, atau masalah lainnya di sini..."></textarea>
        </div>

      </div>

      <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
        <a href="{{ url('/order/' . $order['id_order_produksi'] . '/detail') }}" class="btn-outline-df">Batal</a>
        <button type="submit" class="btn-df">Kirim Tiket</button>
      </div>
  </form>
</div>
</div>
@endsection

@push('scripts')
{{-- Load CKEditor --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
<script>
  $(document).ready(function() {
    const $skeleton = $('#ckeditor-skeleton');
    const $textarea = $('#deskripsi');

    CKEDITOR.ClassicEditor.create(document.querySelector('#deskripsi'), {
        toolbar: { // Toolbar sederhana untuk klien
          items: [
            'bold', 'italic', 'underline', '|',
            'bulletedList', 'numberedList', '|',
            'imageUpload', 'link', '|',
            'undo', 'redo'
          ]
        },
        ckfinder: { // Menggunakan config 'ckfinder'
          uploadUrl: '{{ url("/ajax/upload/wysiwyg") }}'
        },
        placeholder: 'Jelaskan keluhan Anda dan tempel (paste) bukti foto di sini...',
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
{{-- Style untuk CKEditor --}}
<style>
  .ck-editor__editable {
    min-height: 250px;
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
</style>
@endpush