{{-- templates/tim_marketing/prospek_saya/edit.php --}}
@extends('layouts.admin')

@section('title')
Manajemen Prospek
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
    <span>
        /
    </span>
    <span class="text-gray-600">
        Tambah catatan
    </span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
    <div class="w-full space-y-6">
        <div class="card-df">
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="txt-title-df">Narahubung</label>
                        <p class="txt-desc-df">{{ $prospek['narahbung'] ?? $prospek['narahubung'] }} ( {{ $prospek['no_narahubung'] }})</p>
                    </div>
                    <div>
                        <label class="txt-title-df">Nama Sekolah</label>
                        <p class="txt-desc-df">{{ $sekolah['nama'] }}</p>
                    </div>
                    <div>
                        <label class="txt-title-df">Staff Penanggung Jawab</label>
                        <p class="txt-desc-df">{{ $staff['nama'] }} <span class="text-primary text-sm">({{ $is_my_job ? 'Saya' : format_role_name($staff['role']) }})</span></p>
                    </div>
                    <div>
                        <label class="txt-title-df">Status Prospek</label>
                        <p class="txt-desc-df">{!! generate_status_badge($prospek['status_prospek']) !!} </p>
                    </div>
                    <div class="col-span-2">
                        <label class="txt-title-df">Deskripsi</label>
                        <p style="font-size: 1rem" class="txt-desc-df">{{ $prospek['deskripsi'] ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-df">
            <form action="{{ url('/tim-marketing/prospek-saya/' . $prospek['id_prospek'] . '/update') }}" method="POST">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6"> 
                        <div class="col-span-1"> 
                            <label for="catatan" class="block mb-2 text-lg font-medium text-gray-800">
                                Catatan
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
                                name="catatan"
                                id="catatan"
                                rows="4"
                                class="input-df resize-none hidden" 
                                placeholder="Tulis catatan tindak lanjut di sini...">{{ $prospek['catatan'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <a
                        href="{{ url('tim-marketing/prospek-saya') }}"
                        class="btn-outline-df">
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="btn-df">
                        Update Catatan
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
        const $textarea = $('#catatan');

        CKEDITOR.ClassicEditor.create(document.querySelector('#catatan'), {
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
            simpleUpload: {
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
                'Comments','TrackChanges','TrackChangesData','TrackChangesEditing',
                'RealTimeCollaborativeComments','RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeEditing','RealTimeCollaborativeRevisionHistory',
                'RevisionHistory','PresenceList','UsersInit','CKFinder','WProofreader',
                'DocumentOutline','TableOfContents','AIAssistant','MultiLevelList',
                'Pagination','FormatPainter','Template','SlashCommand',
                'PasteFromOfficeEnhanced','CaseChange'
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
        min-height: 250px;
    }
    .ck.ck-editor__main > .ck-editor__editable:focus {
        border-color: var(--color-primary);
        border-color: #4F46E5; 
        box-shadow: 0 0 0 1px #4F46E5;
    }
    .ck.ck-editor__main > .ck-editor__editable,
    .ck.ck-editor__editable.ck-focused {
        border-radius: 0.375rem;
        border-color: #D1D5DB;
    }
</style>
@endpush