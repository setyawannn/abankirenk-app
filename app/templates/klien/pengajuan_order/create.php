@extends('layouts.admin')

@section('title')
Buat Pengajuan Order
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <span>
    /
  </span>
  <span class="text-gray-600">
    Buat Pengajuan Baru
  </span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <div class="card-df">
    <form action="{{ url('/klien/pengajuan-order/store') }}" method="POST">
      <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="narahubung" class="label-df">
              Nama Narahubung <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              name="narahubung"
              id="narahubung"
              class="input-df"
              placeholder="Contoh: Budi Santoso"
              required>
          </div>
          <div>
            <label for="no_narahubung" class="label-df">
              Nomor Telepon (WhatsApp) <span class="text-red-500">*</span>
            </label>
            <input
              type="tel"
              name="no_narahubung"
              id="no_narahubung"
              class="input-df"
              placeholder="Contoh: 081234567890"
              pattern="[0-9]+"
              required>
          </div>

          <div class="col-span-2">
            <label for="id_sekolah" class="label-df">
              Sekolah <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-3">
              <div class="flex-1">
                <select
                  id="id_sekolah"
                  name="id_sekolah"
                  class="input-df"
                  required>
                  <option value="">Pilih Sekolah...</option>
                </select>
              </div>
            </div>
            <div class="flex items-center gap-2 mt-2">
              <input type="checkbox" value="" id="add-new-school-checkbox">
              <p style="margin-top: 0;" class="desc-df">Centang jika sekolah Anda tidak ada di daftar.</p>
            </div>
          </div>

          <div id="new-school-fields" class="col-span-2 grid grid-cols-2 gap-2 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" style="display: none;">
            <div>
              <label for="nama_sekolah" class="label-df">
                Nama Sekolah <span class="text-red-500">*</span>
              </label>
              <input type="text" name="nama_sekolah" id="nama_sekolah" class="input-df" placeholder="Contoh: SMA Negeri 1 Kota Anda" required>
            </div>
            <div>
              <label for="kontak_sekolah" class="label-df">
                Nomor Sekolah (Opsional)
              </label>
              <input type="tel" name="kontak_sekolah" id="kontak_sekolah" class="input-df" placeholder="Contoh: (021) 123456">
            </div>
            <div class="col-span-2">
              <label for="lokasi_sekolah" class="label-df">
                Alamat/Lokasi Sekolah <span class="text-red-500">*</span>
              </label>
              <textarea name="lokasi_sekolah" id="lokasi_sekolah" rows="3" class="input-df resize-none" placeholder="Contoh: Jl. Merdeka No. 10, Kota Anda"></textarea>
            </div>
          </div>

          <div class="col-span-2">
            <label for="pesan" class="label-df">
              Pesan/Keterangan <span class="text-red-500">*</span>
            </label>
            <div id="ckeditor-skeleton" class="border border-gray-300 rounded-md shadow-sm">
              <div class="animate-pulse h-12 bg-gray-200 border-b border-gray-300 rounded-t-md"></div>
              <div class="animate-pulse p-4 space-y-3" style="min-height: 250px;">
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                <div class="h-4 bg-gray-200 rounded"></div>
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              </div>
            </div>
            <textarea
              name="pesan"
              id="pesan"
              rows="4"
              class="input-df resize-none hidden"
              placeholder="Contoh: Kami tertarik untuk membuat yearbook angkatan 2025/2026. Mohon hubungi kami untuk presentasi..."></textarea>
          </div>
        </div>
      </div>

      <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
        <a
          href="{{ url('/klien/pengajuan-order') }}"
          class="btn-outline-df">
          Batal
        </a>
        <button
          type="submit"
          class="btn-df">
          Kirim Pengajuan
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
@push('scripts')
<link href="{{ url('/css/tom-select.css') }} " rel="stylesheet">
<script src="{{ url('/js/tom-select.js') }}"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

<script>
  $(document).ready(function() {
    const checkboxAddNewSchool = $('#add-new-school-checkbox');
    const newSchoolFields = $('#new-school-fields');
    const newSchoolInputs = newSchoolFields.find('input, textarea');

    const tomSelectInstance = new TomSelect('#id_sekolah', {
      valueField: 'id_sekolah',
      labelField: 'nama',
      searchField: 'nama',
      create: false,
      placeholder: 'Ketik untuk mencari sekolah...',
      load: function(query, callback) {
        if (!query.length) return callback();
        $.ajax({
          url: '{{ url("/ajax/sekolah") }}',
          data: {
            q: query
          },
          success: function(response) {
            callback(response);
          },
          error: function() {
            callback();
          }
        });
      },
      render: {
        option: function(data, escape) {
          return '<div class="py-2 px-3 hover:bg-gray-50">' +
            '<div class="font-medium text-gray-900">' + escape(data.nama) + '</div>' +
            (data.lokasi ? '<div class="text-xs text-gray-500 mt-0.5">' + escape(data.lokasi) + '</div>' : '') +
            '</div>';
        },
        item: function(data, escape) {
          return '<div>' + escape(data.nama) + '</div>';
        }
      }
    });

    newSchoolInputs.prop('disabled', true);

    checkboxAddNewSchool.change(function() {
      if (this.checked) {
        newSchoolFields.show();
        newSchoolInputs.prop('disabled', false);

        tomSelectInstance.clear();
        tomSelectInstance.disable();
      } else {
        newSchoolFields.hide();
        newSchoolInputs.prop('disabled', true);

        tomSelectInstance.enable();
      }
    });

    $('#no_narahubung, #kontak_sekolah').on('input', function() {
      this.value = this.value.replace(/[^0-9+()-]/g, '');
    });

    const $skeleton = $('#ckeditor-skeleton');
    const $textarea = $('#pesan');

    CKEDITOR.ClassicEditor.create(document.querySelector('#pesan'), {
        toolbar: {
          items: [
            'heading', '|',
            'bold', 'italic', 'strikethrough', 'underline', 'link', '|',
            'bulletedList', 'numberedList', '|',
            'outdent', 'indent', '|',
            'imageUpload', 'insertTable', 'blockQuote', 'mediaEmbed', 'codeBlock', '|',
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
        placeholder: 'Tulis pesan tindak lanjut di sini...',
        removePlugins: [
          'Comments', 'TrackChanges', 'TrackChangesData', 'TrackChangesEditing',
          'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
          'RealTimeCollaborativeEditing', 'RealTimeCollaborativeRevisionHistory',
          'RevisionHistory', 'PresenceList', 'UsersInit', 'WProofreader',
          'DocumentOutline', 'TableOfContents', 'AIAssistant', 'MultiLevelList',
          'Pagination', 'FormatPainter', 'Template', 'SlashCommand',
          'PasteFromOfficeEnhanced', 'CaseChange', 'ExportPdf', 'ExportWord', 'ImportWord',
          'SimpleUploadAdapter',
          'Base64UploadAdapter'
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
</style>
@endpush