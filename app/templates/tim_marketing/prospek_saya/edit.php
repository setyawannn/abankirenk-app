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
                        <label class="txt-title-df">
                            Narahubung
                        </label>
                        <p class="txt-desc-df">{{ $prospek['narahubung']  }} ( {{ $prospek['no_narahubung'] }})</p>
                    </div>
                    <div>
                        <label class="txt-title-df">
                            Nama Sekolah
                        </label>
                        <p class="txt-desc-df">{{ $sekolah['nama'] }}</p>
                    </div>
                    <div>
                        <label class="txt-title-df">
                            Staff Penanggung Jawab
                        </label>
                        <p class="txt-desc-df">{{ $staff['nama'] }} <span class="text-primary text-sm">({{ $is_my_job ? 'Saya' : $staff['role'] }})</span></p>
                    </div>
                    <div>
                        <label class="txt-title-df">
                            Status Prospek
                        </label>
                        <p class="txt-desc-df">{!! $prospek['status_badge'] !!} </p>
                    </div>
                    <div class="col-span-2">
                        <label class="txt-title-df">
                            Deskripsi
                        </label>
                        <p class="txt-desc-df">{{ $prospek['deskripsi'] ?? '' }}</p>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="card-df">
            <form action="{{ url('/manajer-marketing/manajemen-prospek/' . $prospek['id_prospek'] . '/update') }}" method="POST">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label for="catatan" class="label-df">
                                Catatan
                            </label>
                            <textarea
                                name="catatan"
                                id="catatan"
                                rows="4"
                                class="input-df resize-none"
                                placeholder="Contoh: Client ingin mendapatkan sosialisasi secepatnya sebelum akhir desember dan harus..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <a
                        href="{{ url('manajer-marketing/manajemen-prospek') }}"
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
<link href="{{ url('/css/tom-select.css') }}" rel="stylesheet">
<script src="{{ url('/js/tom-select.js') }}"></script>
<script>
    $(document).ready(function() {
        const checkboxAddNewSchool = $('#add-new-school-checkbox');
        const newSchoolFields = $('#new-school-fields');
        const newSchoolInputs = newSchoolFields.find('input, textarea');

        const initialSchool = {!! json_encode($sekolah ?? null) !!};

        const tomSelectInstance = new TomSelect('#id_sekolah', {
            valueField: 'id_sekolah',
            labelField: 'nama',
            searchField: 'nama',            
            options: initialSchool ? [initialSchool] : [],
            
            items: initialSchool ? [initialSchool.id_sekolah] : [],

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

        $('#no_narahubung').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $('#kontak_sekolah').on('input', function() {
            this.value = this.value.replace(/[^0-ind+()-]/g, '');
        });
    });
</script>
@endpush


@push('styles')
<style>
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
@endpush