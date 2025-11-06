<!-- templates/manajer_marketing/manajemen_prospek/create.php -->
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
        Tambah Prospek Baru
    </span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
    <div class="card-df">
        <form action="{{ url('/manajer-marketing/manajemen-prospek/store') }}" method="POST">
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
                            placeholder="Contoh: Sri Wahyuni"
                            required>
                    </div>
                    <div>
                        <label for="no_narahubung" class="label-df">
                            Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="tel"
                            name="no_narahubung"
                            id="no_narahubung"
                            class="input-df"
                            placeholder="Contoh: 081333717212"
                            pattern="[0-9]+"
                            required>
                        <p class="desc-df">Format: nomor telepon tanpa spasi atau tanda hubung</p>
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
                            <p style="margin-top: 0;" class="desc-df">Jika sekolah tidak ada centak untuk memasukan data sekolah</p>
                        </div>
                    </div>

                    <div id="new-school-fields" class="col-span-2 grid grid-cols-2 gap-2 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" style="display: none;">
                        <div>
                            <label for="nama_sekolah" class="label-df">
                                Nama Sekolah <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="nama_sekolah"
                                id="nama_sekolah"
                                class="input-df"
                                placeholder="Contoh: SMAK Kedung Badak Malang"
                                required>
                        </div>
                        <div>
                            <label for="kontak_sekolah" class="label-df">
                                Nomor Sekolah <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="tel"
                                name="kontak_sekolah"
                                id="kontak_sekolah"
                                class="input-df"
                                placeholder="Contoh: (0341) 712345, 021-555-1234 atau 081234567890"
                                required>
                        </div>
                        <div class="col-span-2">
                            <label for="lokasi_sekolah" class="label-df">
                                Lokasi Sekolah <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="lokasi_sekolah"
                                id="lokasi_sekolah"
                                rows="4"
                                class="input-df resize-none"
                                placeholder="Contoh: Jalan danau poso 1 G2E No 16, Sawojajar, Kedungkandang, Malang"></textarea>
                        </div>
                    </div>


                    <div class="col-span-2">
                        <label for="id_user" class="label-df">
                            Staf Penanggung Jawab (PIC)
                        </label>
                        <select
                            name="id_user"
                            id="id_user"
                            class="input-df">
                            <option value="">Pilih Staf Marketing...</option>
                            @foreach($staff as $st)
                            <option value="{{ $st['id_user'] }}">{{ $st['nama'] }}</option>
                            @endforeach
                        </select>
                        <p class="desc-df">Pilih tim marketing yang akan menangani prospek ini</p>
                    </div>


                    <div class="col-span-2">
                        <label for="catatan" class="label-df">
                            Deskripsi
                        </label>
                        <textarea
                            name="catatan"
                            id="catatan"
                            rows="4"
                            class="input-df resize-none"
                            placeholder="Contoh: Client ingin mendapatkan sosialisasi secepatnya sebelum akhir desember dan harus..."></textarea>
                        <p class="desc-df">Tambahkan informasi detail tentang kebutuhan client atau karakteristik khusus</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <a
                    href="{{ url('manajer-marketing/manajemen-prospek') }}"
                    class="btn-outline-df">
                    Batal
                </a>
                <button
                    type="submit"
                    class="btn-df">
                    Simpan Prospek
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<link href="{{ url('/css/tom-select.css') }} " rel="stylesheet">
<script src="{{ url('/js/tom-select.js') }}"></script>
<script>
    $(document).ready(function() {
        const modal = $('#modal-add-sekolah');
        const loadingOverlay = $('#loading-overlay');
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

        $('#no_narahubung').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $('#kontak_sekolah').on('input', function() {
            this.value = this.value.replace(/[^0-9+()-]/g, '');
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