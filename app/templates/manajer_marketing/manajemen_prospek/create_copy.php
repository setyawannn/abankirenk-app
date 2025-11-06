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
    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ url('/manajer-marketing/manajemen-prospek/store') }}" method="POST">
            <div class="p-6 space-y-6">
                <!-- Contact Information Section -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kontak</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="narahubung" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Narahubung <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="narahubung" 
                                id="narahubung" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm placeholder-gray-400"
                                placeholder="Contoh: Sri Wahyuni"
                                required
                            >
                        </div>
                        <div>
                            <label for="no_narahubung" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                name="no_narahubung" 
                                id="no_narahubung" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm placeholder-gray-400"
                                placeholder="Contoh: 081333717212"
                                pattern="[0-9]+"
                                required
                            >
                            <p class="mt-1.5 text-xs text-gray-500">Format: nomor telepon tanpa spasi atau tanda hubung</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- School Information Section -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Sekolah</h2>
                    <div>
                        <label for="id_sekolah" class="block text-sm font-medium text-gray-700 mb-2">
                            Sekolah <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <select 
                                    id="id_sekolah" 
                                    name="id_sekolah" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm"
                                    required
                                >
                                    <option value="">Pilih Sekolah...</option>
                                </select>
                            </div>
                            <button 
                                type="button" 
                                id="btn-add-sekolah" 
                                class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors whitespace-nowrap"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Sekolah
                            </button>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500">Ketik untuk mencari atau tambahkan sekolah baru jika belum terdaftar</p>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Assignment Section -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Penugasan</h2>
                    <div>
                        <label for="id_user" class="block text-sm font-medium text-gray-700 mb-2">
                            Staf Penanggung Jawab (PIC)
                        </label>
                        <select 
                            name="id_user" 
                            id="id_user" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm"
                        >
                            <option value="">Pilih Staf Marketing...</option>
                            @foreach($staff as $st)
                                <option value="{{ $st['id_user'] }}">{{ $st['nama'] }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-xs text-gray-500">Pilih tim marketing yang akan menangani prospek ini</p>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Description Section -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Catatan Tambahan</h2>
                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea 
                            name="catatan" 
                            id="catatan" 
                            rows="4" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm placeholder-gray-400 resize-none"
                            placeholder="Contoh: Client meminta guru matematika untuk SMA, preferensi lulusan S1 Pendidikan Matematika dengan pengalaman minimal 2 tahun..."
                        ></textarea>
                        <p class="mt-1.5 text-xs text-gray-500">Tambahkan informasi detail tentang kebutuhan client atau karakteristik khusus</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <a 
                    href="{{ url('manajer_marketing/prospek') }}" 
                    class="inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                >
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Prospek
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Adding New School -->
<div id="modal-add-sekolah" class="hidden fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div id="modal-overlay" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content">
            <!-- Modal Header -->
            <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-200">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                            Tambah Sekolah Baru
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">Masukkan informasi sekolah yang akan ditambahkan</p>
                    </div>
                    <button type="button" id="btn-close-modal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="bg-white px-6 py-5">
                <form id="form-add-sekolah" class="space-y-5">
                    <div>
                        <label for="nama_sekolah" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Sekolah <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama" 
                            id="nama_sekolah" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm placeholder-gray-400"
                            placeholder="Contoh: SMA Negeri 1 Malang"
                            required
                        >
                    </div>
                    <div>
                        <label for="lokasi_sekolah" class="block text-sm font-medium text-gray-700 mb-2">
                            Lokasi <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="lokasi" 
                            id="lokasi_sekolah" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm placeholder-gray-400"
                            placeholder="Contoh: Jl. Tugu No. 1, Malang"
                            required
                        >
                    </div>
                    <div>
                        <label for="kontak_sekolah" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Kontak <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="tel" 
                            name="kontak" 
                            id="kontak_sekolah" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm placeholder-gray-400"
                            placeholder="Contoh: (0341) 123456"
                            required
                        >
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button 
                    type="button" 
                    id="btn-cancel-sekolah" 
                    class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    id="btn-save-sekolah" 
                    class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Sekolah
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center">
    <div class="bg-white rounded-lg p-6 flex items-center gap-3">
        <svg class="animate-spin h-5 w-5 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm font-medium text-gray-700">Menyimpan data...</span>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
$(document).ready(function() {
    const modal = $('#modal-add-sekolah');
    const loadingOverlay = $('#loading-overlay');
    
    // Initialize TomSelect for school dropdown
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
                data: { q: query },
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

    // Modal open
    $('#btn-add-sekolah').on('click', function() {
        modal.removeClass('hidden');
        $('#nama_sekolah').focus();
    });

    // Modal close handlers
    function closeModal() {
        modal.addClass('hidden');
        $('#form-add-sekolah')[0].reset();
    }

    $('#btn-cancel-sekolah, #btn-close-modal, #modal-overlay').on('click', closeModal);

    // Close on ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && !modal.hasClass('hidden')) {
            closeModal();
        }
    });

    // Prevent closing when clicking inside modal content
    $('.modal-content').on('click', function(e) {
        e.stopPropagation();
    });

    // Save new school
    $('#btn-save-sekolah').on('click', function() {
        const form = $('#form-add-sekolah');
        
        // Validate form
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        loadingOverlay.removeClass('hidden');

        $.ajax({
            url: '{{ url("/ajax/sekolah/store") }}',
            method: 'POST',
            data: form.serialize(),
            success: function(data) {
                loadingOverlay.addClass('hidden');
                
                if (data.success) {
                    // Add to TomSelect and select it
                    tomSelectInstance.addOption(data.sekolah);
                    tomSelectInstance.setValue(data.sekolah.id_sekolah);
                    
                    closeModal();
                    
                    // Show success message
                    showNotification('Sekolah berhasil ditambahkan!', 'success');
                } else {
                    showNotification(data.message || 'Gagal menyimpan sekolah baru', 'error');
                }
            },
            error: function(xhr) {
                loadingOverlay.addClass('hidden');
                const message = xhr.responseJSON?.message || 'Terjadi kesalahan. Silakan coba lagi.';
                showNotification(message, 'error');
            }
        });
    });

    // Notification function
    function showNotification(message, type = 'success') {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const notification = $(`
            <div class="fixed top-4 right-4 z-50 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'}
                </svg>
                <span class="font-medium">${message}</span>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Phone number validation
    $('#no_narahubung, #kontak_sekolah').on('input', function() {
        this.value = this.value.replace(/[^0-9+\-() ]/g, '');
    });
});
</script>

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

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

/* TomSelect custom styling */
.ts-wrapper .ts-control {
    padding: 0.625rem 1rem !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    min-height: 42px !important;
}

.ts-wrapper.focus .ts-control {
    border-color: #4d94df !important;
    box-shadow: 0 0 0 2px rgba(77, 148, 223, 0.2) !important;
}

.ts-dropdown {
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
}
</style>
@endpush