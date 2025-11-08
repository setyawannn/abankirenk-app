@extends('layouts.admin')

@section('title')
Riwayat Pengajuan Order
@endsection

@section('content')
<div class="space-y-6">
  <div class="rounded-lg flex flex-col md:flex-row justify-between items-center gap-4">
    <div class="flex-grow">
      <label for="search-input" class="sr-only">Cari Pengajuan</label>
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <ion-icon name="search-outline" class="text-gray-400"></ion-icon>
        </div>
        <input type="text" id="search-input" class="block w-full md:w-[300px] pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Cari no. pengajuan, sekolah...">
      </div>
    </div>

    <a href="{{ url('/klien/pengajuan-order/create') }}" class="btn-df w-fit md:w-fit">
      <ion-icon name="add-outline" class="-ml-1 mr-2"></ion-icon>
      Buat Pengajuan Baru
    </a>
  </div>

  <div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pengajuan</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sekolah</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Narahubung</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Dibuat</th>
          </tr>
        </thead>
        <tbody id="pengajuan-table-body" class="bg-white divide-y divide-gray-200">
          <tr>
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Memuat data...</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 flex items-center justify-between sm:px-6 rounded-b-lg border-t border-gray-200">
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700" id="pagination-info">Memuat...</p>
        </div>
        <div>
          <nav id="pagination-controls" class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination"></nav>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('modals')
@endpush

@push('scripts')
<script>
  $(document).ready(function() {
    let currentPage = 1;
    let currentSearch = '';
    const limit = 10;

    function fetchData(page = 1) {
      currentPage = page;
      currentSearch = $('#search-input').val();

      $('#pengajuan-table-body').html('<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Memuat data...</td></tr>');
      $('#pagination-controls').empty();

      $.ajax({
        url: "{{ url('/klien/pengajuan-order/ajax-list') }}",
        type: 'GET',
        data: {
          page: currentPage,
          limit: limit,
          search: currentSearch
        },
        dataType: 'json',
        success: function(response) {
          renderTable(response.data);
          renderPagination(response.pagination);
        },
        error: function(xhr) {
          let errorMsg = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Gagal memuat data. Silakan coba lagi.</td></tr>';
          if (xhr.status === 403) {
            errorMsg = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Otentikasi gagal. Silakan muat ulang halaman.</td></tr>';
          }
          $('#pengajuan-table-body').html(errorMsg);
          $('#pagination-info').text('Tidak ada data');
        }
      });
    }

    function renderTable(data) {
      const tableBody = $('#pengajuan-table-body');
      tableBody.empty();

      if (data.length === 0) {
        tableBody.html('<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Anda belum memiliki riwayat pengajuan.</td></tr>');
        return;
      }

      data.forEach((pengajuan) => {
        const row = `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${escapeHTML(pengajuan.nomor_pengajuan || '-')}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${escapeHTML(pengajuan.nama_sekolah)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>${escapeHTML(pengajuan.narahubung)}</div>
                        <div class="text-xs text-gray-400">${escapeHTML(pengajuan.no_narahubung)}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">${pengajuan.status_badge}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${pengajuan.formatted_created_at}</td>
                </tr>
                `;
        tableBody.append(row);
      });
    }

    function renderPagination(pagination) {
      const {
        total,
        per_page,
        current_page,
        last_page
      } = pagination;
      const paginationControls = $('#pagination-controls');
      paginationControls.empty();
      const from = total > 0 ? (current_page - 1) * per_page + 1 : 0;
      const to = Math.min(current_page * per_page, total);

      if (total > 0) {
        $('#pagination-info').html(`Menampilkan <span class="font-medium">${from}</span> sampai <span class="font-medium">${to}</span> dari <span class="font-medium">${total}</span> hasil`);
      } else {
        $('#pagination-info').text('Tidak ada data');
      }

      const prevDisabled = current_page <= 1;
      paginationControls.append(
        `<button data-page="${current_page - 1}" ${prevDisabled ? 'disabled' : ''} class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="sr-only">Previous</span>
                <ion-icon name="chevron-back-outline" class="h-5 w-5"></ion-icon>
                </button>`
      );

      let startPage = Math.max(1, current_page - 2);
      let endPage = Math.min(last_page, current_page + 2);

      if (last_page === 0) {
        startPage = 0;
        endPage = 0;
      }

      if (startPage > 1) {
        paginationControls.append(`<button data-page="1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</button>`);
        if (startPage > 2) {
          paginationControls.append(`<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>`);
        }
      }

      for (let i = startPage; i <= endPage; i++) {
        if (i === 0) continue;
        const activeClass = i === current_page ? 'z-10 bg-primary-50 border-primary text-primary' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50';
        paginationControls.append(
          `<button data-page="${i}" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ${activeClass}">
                    ${i}
                </button>`
        );
      }

      if (endPage < last_page) {
        if (endPage < last_page - 1) {
          paginationControls.append(`<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>`);
        }
        paginationControls.append(`<button data-page="${last_page}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">${last_page}</button>`);
      }

      const nextDisabled = current_page >= last_page;
      paginationControls.append(
        `<button data-page="${current_page + 1}" ${nextDisabled ? 'disabled' : ''} class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="sr-only">Next</span>
                <ion-icon name="chevron-forward-outline" class="h-5 w-5"></ion-icon>
                </button>`
      );
    }

    $('#search-input').on('keyup', debounce(function() {
      fetchData(1);
    }, 500));

    $('#pagination-controls').on('click', 'button[data-page]', function() {
      if (!$(this).prop('disabled')) {
        const page = $(this).data('page');
        fetchData(page);
      }
    });

    fetchData();
  });
</script>
@endpush