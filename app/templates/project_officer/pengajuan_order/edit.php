@extends('layouts.admin')

@section('title')
Pengajuan Order
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <span class="text-primary font-medium">
    Pengajuan Order
  </span>
  <span>
    /
  </span>
  <span class="text-gray-600">
    {{ $pengajuan['nomor_pengajuan'] }}
  </span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <div class="space-y-4">
    <div class="card-df">
      <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="txt-title-df">Nama Sekolah</label>
            <p class="txt-desc-df">{{ $pengajuan['nama_sekolah'] }}</p>
          </div>
          <div>
            <label class="txt-title-df">Dibuat</label>
            <p class="txt-desc-df">{{ $pengajuan['nama_klien'] }} | {{ $pengajuan['formatted_created_at'] }}</p>
          </div>
          <div>
            <label class="txt-title-df">Narahubung</label>
            <p class="txt-desc-df">{{ $pengajuan['narahubung'] }}</p>
          </div>
          <div>
            <label class="txt-title-df">No Narahubung</label>
            <p class="txt-desc-df">{{ $pengajuan['no_narahubung'] }}</p>
          </div>
          <div class="col-span-2">
            <label class="txt-title-df">Detail & Deskripsi</label>
            <div class="txt-editor-df">
              @if (empty($pengajuan['pesan']))
              <p class="italic text-gray-500">Belum ada pesan dari Project Officer.</p>
              @else
              {!! $pengajuan['pesan'] !!}
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-df">
      <form action="{{ url('/project-officer/pengajuan-order/' . $pengajuan['id_pengajuan'] . '/update') }}" method="POST">
        <div class="p-6 space-y-6">
          <div>
            <label for="status_pengajuan" class="label-df">
              Ubah Status Pengajuan <span class="text-red-500">*</span>
            </label>
            <select name="status_pengajuan" id="status_pengajuan" class="input-df" required>
              @foreach($status_options as $status)
              <option value="{{ $status }}" @if($status==$pengajuan['status_pengajuan']) selected @endif>
                {{ ucwords(str_replace('_', ' ', $status)) }}
              </option>
              @endforeach
            </select>
            <p class="desc-df">Klien akan melihat status ini.</p>
          </div>

          <div>
            <label for="balasan" class="label-df">
              Balasan untuk Klien
            </label>
            <div id="ckeditor-skeleton" class="border border-gray-300 rounded-md shadow-sm">
              <div class="animate-pulse h-12 bg-gray-200 border-b border-gray-300 rounded-t-md"></div>
              <div class="animate-pulse p-4 space-y-3" style="min-height: 200px;">
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
              </div>
            </div>
            <textarea
              name="balasan"
              id="balasan"
              rows="8"
              class="input-df resize-none hidden"
              placeholder="Tulis balasan atau catatan internal di sini...">{{ $pengajuan['balasan'] ?? '' }}</textarea>
          </div>

        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
          <a
            href="{{ url('/project-officer/pengajuan-order') }}"
            class="btn-outline-df">
            Batal
          </a>
          <button
            type="submit"
            class="btn-df">
            Update Pengajuan
          </button>
        </div>
      </form>
    </div>


  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

<script>
  $(document).ready(function() {
    const $skeleton = $('#ckeditor-skeleton');
    const $textarea = $('#balasan');

    CKEDITOR.ClassicEditor.create(document.querySelector('#balasan'), {
        toolbar: {
          items: [
            'heading', '|',
            'bold', 'italic', 'strikethrough', 'underline', 'link', '|',
            'bulletedList', 'numberedList', '|',
            'outdent', 'indent', '|',
            'imageUpload',
            'insertTable', 'blockQuote', 'mediaEmbed', 'codeBlock', '|',
            'undo', 'redo', '|',
            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
            'alignment', 'todoList', 'horizontalLine'
          ],
          shouldNotGroupWhenFull: true
        },
        ckfinder: {
          uploadUrl: '{{ url("/ajax/upload/wysiwyg") }}'
        },

        list: {
          properties: {
            styles: true,
            startIndex: true,
            reversed: true
          }
        },
        placeholder: 'Tulis catatan tindak lanjut di sini...',
        removePlugins: [
          'Comments', 'TrackChanges', 'TrackChangesData', 'TrackChangesEditing',
          'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
          'RealTimeCollaborativeEditing', 'RealTimeCollaborativeRevisionHistory',
          'RevisionHistory', 'PresenceList', 'UsersInit', 'WProofreader',
          'DocumentOutline', 'TableOfContents', 'AIAssistant', 'MultiLevelList',
          'Pagination', 'FormatPainter', 'Template', 'SlashCommand',
          'PasteFromOfficeEnhanced', 'CaseChange', 'ExportPdf', 'ExportWord', 'ImportWord',
        ]

      })
      .then(editor => {
        console.log('CKEditor 5 berhasil dimuat.');
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

  .prose {
    color: #374151;
  }

  .prose p {
    margin-top: 0;
    margin-bottom: 1em;
  }

  .prose ul,
  .prose ol {
    margin-left: 1.25em;
  }
</style>
@endpush