@extends('layouts.admin')

@section('title')
Dashboard
@endsection

@section('content')
<div class="space-y-6">
    {{-- Filter dan Search Bar --}}
    <div class="rounded-lg flex items-center space-x-4">
        <div class="flex-grow">
            <label for="search-prospek" class="sr-only">Cari Prospek</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <ion-icon name="search-outline" class="text-gray-400"></ion-icon>
                </div>
                <input type="text" id="search-prospek" class="block w-[300px] pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Cari nama sekolah atau catatan...">
            </div>
        </div>
        <div>
            <label for="status-filter" class="sr-only">Filter Status</label>
            <select id="status-filter" class="input-df">
                <option value="">Semua Status</option>
                @foreach ($status_options as $status)
                <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
        </div>
        <a href="{{ url('manajer-marketing/manajemen-prospek/create') }}" class="btn-df">
            <ion-icon name="add-outline" class="-ml-1 mr-2"></ion-icon>
            Tambah Prospek
        </a>
    </div>

    {{-- Tabel Prospek --}}
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Sekolah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>
                <tbody id="prospek-table-body" class="bg-white divide-y divide-gray-200">
                    {{-- Baris akan diisi oleh AJAX --}}
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
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
                        {{-- Tombol pagination akan diisi oleh AJAX --}}
                    </nav>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

@push('scripts')
<script src="{{ url('/js/helper.js') }}"></script>
<script>
    $(document).ready(function() {
        let currentPage = 1;
        let currentSearch = '';
        let currentStatus = '';
        const limit = 10; // Jumlah item per halaman

        // Fungsi utama untuk mengambil dan merender data
        function fetchData(page = 1) {
            currentPage = page;
            currentSearch = $('#search-prospek').val();
            currentStatus = $('#status-filter').val();

            // Tampilkan loading
            $('#prospek-table-body').html('<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Memuat data...</td></tr>');
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
                    renderTable(response.data);
                    renderPagination(response.pagination);
                },
                error: function() {
                    $('#prospek-table-body').html('<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Gagal memuat data. Silakan coba lagi.</td></tr>');
                    $('#pagination-info').text('Tidak ada data');
                }
            });
        }

        function renderTable(data) {
            const tableBody = $('#prospek-table-body');
            tableBody.empty(); // Kosongkan tabel

            if (data.length === 0) {
                tableBody.html('<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data prospek ditemukan.</td></tr>');
                return;
            }

            data.forEach(prospek => {
                const row = `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${prospek.id_prospek}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${prospek.nama_sekolah}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">${prospek.status_badge}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">${prospek.catatan || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="#" class="text-primary hover:text-primary-700">Edit</a>
                    </td>
                </tr>
            `;
                tableBody.append(row);
            });
        }

        // Fungsi untuk merender kontrol pagination
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

            // Tombol Previous (Desktop & Mobile)
            const prevDisabled = current_page <= 1;
            paginationControls.append(`
            <button data-page="${current_page - 1}" ${prevDisabled ? 'disabled' : ''} class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="sr-only">Previous</span>
                <ion-icon name="chevron-back-outline" class="h-5 w-5"></ion-icon>
            </button>
        `);
            $('#prev-mobile').prop('disabled', prevDisabled).data('page', current_page - 1);


            // Tombol Halaman (Maksimal 5 tombol untuk sederhana)
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


            // Tombol Next (Desktop & Mobile)
            const nextDisabled = current_page >= last_page;
            paginationControls.append(`
            <button data-page="${current_page + 1}" ${nextDisabled ? 'disabled' : ''} class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="sr-only">Next</span>
                <ion-icon name="chevron-forward-outline" class="h-5 w-5"></ion-icon>
            </button>
        `);
            $('#next-mobile').prop('disabled', nextDisabled).data('page', current_page + 1);
        }



        // Listener untuk search input (dengan debounce)
        $('#search-prospek').on('keyup', debounce(function() {
            fetchData(1); // Kembali ke halaman 1 setiap kali search
        }, 500)); // Tunggu 500ms setelah user berhenti mengetik

        // Listener untuk filter status
        $('#status-filter').on('change', function() {
            fetchData(1); // Kembali ke halaman 1 setiap kali filter berubah
        });

        // Listener untuk klik tombol pagination (desktop & mobile)
        $('#pagination-controls, #prev-mobile, #next-mobile').on('click', 'button[data-page]', function() {
            if (!$(this).prop('disabled')) {
                const page = $(this).data('page');
                fetchData(page);
            }
        });

        // --- Inisialisasi ---
        fetchData(); // Panggil pertama kali saat halaman dimuat
    });
</script>
@endpush