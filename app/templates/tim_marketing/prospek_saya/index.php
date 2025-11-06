<!-- template/manejer_marketing/manajemen_prospek/index.php -->
@extends('layouts.admin')

@section('title')
Dashboard
@endsection

@section('content')
<div class="space-y-6">
    {{-- Filter dan Search Bar --}}
    <div class="rounded-lg flex items-center space-x-4">
        <div>
            <label for="status-filter" class="sr-only">Filter Status</label>
            <select id="status-filter" class="input-df bg-white">
                <option value="">Semua Status</option>
                @foreach ($status_options as $status)
                <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-grow">
            <label for="search-prospek" class="sr-only">Cari Prospek</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <ion-icon name="search-outline" class="text-gray-400"></ion-icon>
                </div>
                <input type="text" id="search-prospek" class="block w-[300px] pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Cari nama sekolah atau catatan...">
            </div>
        </div>

    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">

        <div id="prospect-card-container" class="divide-y divide-gray-200 px-3 py-4"></div>

        <div class="px-4 py-3 flex items-center justify-between sm:px-6 rounded-b-lg">
            <div class="flex-1 flex justify-between sm:hidden">
                <button id="prev-mobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50" disabled> Previous </button>
                <button id="next-mobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50" disabled> Next </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700" id="pagination-info">
                        Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">0</span> results
                    </p>
                </div>
                <div>
                    <nav id="pagination-controls" class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<div
    id="modal-konfirmasi-status"
    class="modal-container fixed inset-0 z-40 p-4
           flex items-center justify-center 
           invisible">
    <div class="modal-overlay fixed inset-0 bg-gray-900/50 backdrop-blur-sm
                opacity-0 transition-opacity duration-300 ease-out">
    </div>
    <div class="modal-box relative z-50 w-full max-w-md rounded-lg bg-white p-6 shadow-xl 
                opacity-0 scale-95 transition-all duration-300 ease-out">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold text-gray-900">
                Konfirmasi Ubah Status
            </h3>
            <button
                type="button"
                data-modal-dismiss="#modal-konfirmasi-status"
                class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                <ion-icon name="close" class="h-6 w-6"></ion-icon>
            </button>
        </div>
        <div class="mt-4">
            <p class="text-gray-600">
                Anda yakin ingin mengubah status prospek ini dari
                <strong id="modal-status-old" class="font-medium text-gray-900">...</strong>
                menjadi
                <strong id="modal-status-new" class="font-medium text-gray-900">...</strong>?
            </p>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button
                type="button"
                data-modal-dismiss="#modal-konfirmasi-status"
                class="btn-outline-df">
                Batal
            </button>
            <button
                type="button"
                id="btn-confirm-status-update"
                class="btn-df">
                Ya, Update
            </button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script src="{{ url('/js/helper.js') }}"></script>
<script>
    $(document).ready(function() {
        let currentPage = 1;
        let currentSearch = '';
        let currentStatus = '';
        const limit = 5;

        const allStatusOptions = {!! json_encode($status_options ?? null) !!};
        
        function fetchData(page = 1) {
            currentPage = page;
            currentSearch = $('#search-input').val();
            currentStatus = $('#status-filter').val();

            $('#prospect-card-container').html('<div class="p-6 text-center text-gray-500">Memuat data...</div>');
            $('#pagination-controls').empty();
            $('#prev-mobile, #next-mobile').prop('disabled', true);

            $.ajax({
                url: "{{ url('/ajax/prospek') }}",
                type: 'GET',
                data: {
                    page: currentPage,
                    limit: limit,
                    search: currentSearch,
                    status: currentStatus
                },
                dataType: 'json',
                success: function(response) {
                    renderCards(response.data);
                    renderPagination(response.pagination);
                },
                error: function() {
                    $('#prospect-card-container').html('<div class="p-6 text-center text-red-500">Gagal memuat data. Silakan coba lagi.</div>');
                    $('#pagination-info').text('Tidak ada data');
                }
            });
        }

        function renderCards(data) {
            const cardContainer = $('#prospect-card-container');
            cardContainer.empty();

            if (data.length === 0) {
                cardContainer.html('<div class="p-6 text-center text-gray-500">Tidak ada data prospek ditemukan.</div>');
                return;
            }

            data.forEach((prospek, index) => {
                const detail = `{{ url('/tim-marketing/prospek-saya') }}/${prospek.id_prospek}`;

                let statusSelectHtml = `<select class="input-df bg-white open-status-modal" data-id="${prospek.id_prospek}" data-original-value="${prospek.status_prospek}">`;
                allStatusOptions.forEach(function(status) {
                    const selected = (status === prospek.status_prospek) ? 'selected' : '';
                    const statusText = status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
                    statusSelectHtml += `<option value="${status}" ${selected}>${statusText}</option>`;
                });
                statusSelectHtml += '</select>';

                const cardHtml = `
                    <div class="flex flex-col md:flex-row items-start gap-4 p-4 w-full">
                        
                        <div class="w-full md:w-1/5">
                            ${statusSelectHtml}
                        </div>

                        <div class="flex flex-col gap-1 w-full md:w-3/5">
                            <h4 class="text-lg font-medium text-gray-600">${prospek.nama_sekolah}</h4>
                            <div class="flex items-center gap-2 text-sm font-normal text-gray-500">
                                <ion-icon name="id-card-outline" class="h-5 w-5 text-primary"></ion-icon>
                                <span>${prospek.narahubung} (${prospek.no_narahubung})</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                                ${prospek.deskripsi || 'Tidak ada deskripsi.'}
                            </p>
                        </div>

                        <div class="w-1/5 flex flex-col items-end gap-2">
                            <div class="text-sm text-gray-500 text-end">
                                <p>Terakhir diperbarui</p>
                                <p class="text-primary">${prospek.formatted_created_at}</p>
                            </div>
                            <div>
                                <a href="${detail}" class="bg-primary rounded-md p-2 hover:bg-primary-700 flex items-center justify-center">

                                    <ion-icon name="create-outline" class="h-5 w-5 text-white"></ion-icon>
                                </a>
                            </div>
                        </div>
                    </div>
                `;

                cardContainer.append(cardHtml);
            });
        }

        $(document).on('change', '.open-status-modal', function() {
            const $select = $(this);
            const prospectId = $select.data('id');
            const originalStatus = $select.data('original-value');
            const newStatus = $select.val();
            
            const originalStatusText = $select.find(`option[value="${originalStatus}"]`).text().trim();
            const newStatusText = $select.find(`option[value="${newStatus}"]`).text().trim();

            $('#modal-status-old').text(originalStatusText);
            $('#modal-status-new').text(newStatusText);

            $('#btn-confirm-status-update').data('id', prospectId);
            $('#btn-confirm-status-update').data('status', newStatus);

            showModal('#modal-konfirmasi-status');

            $select.val(originalStatus);
        });

        $('#btn-confirm-status-update').on('click', function() {
            const $button = $(this);
            const prospectId = $button.data('id');
            const newStatus = $button.data('status');

            hideModal('#modal-konfirmasi-status');
            
            $.ajax({
                url: '{{ url("/ajax/prospek/update-status") }}',
                type: 'POST',
                data: {
                    id_prospek: prospectId,
                    status: newStatus
                },
                success: function(response) {
                    showGlobalToast('success', 'Update Berhasil', response.message);
                    fetchData(currentPage);
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memperbarui status.';
                    showGlobalToast('error', 'Update Gagal', errorMsg);
                }
            });
        });

        function renderPagination(pagination) {
            const {
                total,
                per_page,
                current_page,
                last_page
            } = pagination;
            const paginationControls = $('#pagination-controls');
            paginationControls.empty();

            const from = (current_page - 1) * per_page + 1;
            const to = Math.min(current_page * per_page, total);

            if (total > 0) {
                $('#pagination-info').html(`Menampilkan <span class="font-medium">${from}</span> sampai <span class="font-medium">${to}</span> dari <span class="font-medium">${total}</span> hasil`);
            } else {
                $('#pagination-info').text('Tidak ada data');
            }

            const prevDisabled = current_page <= 1;
            paginationControls.append(`
            <button data-page="${current_page - 1}" ${prevDisabled ? 'disabled' : ''} class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="sr-only">Previous</span>
                <ion-icon name="chevron-back-outline" class="h-5 w-5"></ion-icon>
            </button>
        `);
            $('#prev-mobile').prop('disabled', prevDisabled).data('page', current_page - 1);


            let startPage = Math.max(1, current_page - 2);
            let endPage = Math.min(last_page, current_page + 2);

            if (startPage > 1) {
                paginationControls.append(`<button data-page="1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</button>`);
                if (startPage > 2) {
                    paginationControls.append(`<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>`);
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === current_page ? 'z-10 bg-primary-50 border-primary text-primary' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50';
                paginationControls.append(`
                <button data-page="${i}" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ${activeClass}">
                    ${i}
                </button>
            `);
            }

            if (endPage < last_page) {
                if (endPage < last_page - 1) {
                    paginationControls.append(`<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>`);
                }
                paginationControls.append(`<button data-page="${last_page}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">${last_page}</button>`);
            }


            const nextDisabled = current_page >= last_page;
            paginationControls.append(`
            <button data-page="${current_page + 1}" ${nextDisabled ? 'disabled' : ''} class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="sr-only">Next</span>
                <ion-icon name="chevron-forward-outline" class="h-5 w-5"></ion-icon>
            </button>
        `);
            $('#next-mobile').prop('disabled', nextDisabled).data('page', current_page + 1);
        }



        $('#search-prospek').on('keyup', debounce(function() {
            fetchData(1);
        }, 500));

        $('#status-filter').on('change', function() {
            fetchData(1);
        });

        $('#pagination-controls, #prev-mobile, #next-mobile').on('click', 'button[data-page]', function() {
            if (!$(this).prop('disabled')) {
                const page = $(this).data('page');
                fetchData(page);
            }
        });

        fetchData();
    });
</script>
@endpush