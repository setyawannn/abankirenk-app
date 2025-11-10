@extends('layouts.admin')

@section('title')
Manajemen Order Produksi
@endsection

@section('content')
<div class="space-y-6">
  <div class="rounded-lg flex flex-col md:flex-row justify-between items-center gap-4">

    {{-- Filter --}}
    <div class="flex-grow flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
      <div class="relative w-full md:w-auto">
        <label for="search-input" class="sr-only">Cari Order</label>
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <ion-icon name="search-outline" class="text-gray-400"></ion-icon>
        </div>
        <input type="text" id="search-input" class="block w-full md:w-[300px] pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Cari No. Order, Sekolah, Narahubung...">
      </div>
      <div class="w-full md:w-auto">
        <label for="status-filter" class="sr-only">Filter Status</label>
        <select id="status-filter" class="input-df bg-white w-full md:w-48">
          <option value="">Semua Status</option>
          @foreach ($status_options as $status)
          <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Tombol Tambah --}}
    <a href="{{ url('/project-officer/order/create') }}" class="btn-df w-full md:w-auto flex-shrink-0">
      <ion-icon name="add-outline" class="-ml-1 mr-2"></ion-icon>
      Buat Order Baru
    </a>
  </div>

  {{-- Tabel Order --}}
  <div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Order</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sekolah</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Narahubung</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diperbarui</th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">Aksi</span>
            </th>
          </tr>
        </thead>
        <tbody id="order-table-body" class="bg-white divide-y divide-gray-200">
          <tr>
            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Memuat data...</td>
          </tr>
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="px-4 py-3 flex items-center justify-between sm:px-6 rounded-b-lg border-t border-gray-200">
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700" id="pagination-info">
            Memuat...
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
{{-- Modal Hapus (jika diperlukan nanti) --}}
@endpush

@push('scripts')
{{-- Asumsi helper.js (debounce, escapeHTML) dimuat di admin.php --}}
<script>
  $(document).ready(function() {
    let currentPage = 1;
    let currentSearch = '';
    let currentStatus = ''; // Filter status
    const limit = 10;

    function fetchData(page = 1) {
      currentPage = page;
      currentSearch = $('#search-input').val();
      currentStatus = $('#status-filter').val(); // Ambil nilai status

      $('#order-table-body').html('<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Memuat data...</td></tr>');
      $('#pagination-controls').empty();

      $.ajax({
        url: "{{ url('/ajax/po/order-list') }}", // <-- URL AJAX BARU
        type: 'GET',
        data: {
          page: currentPage,
          limit: limit,
          search: currentSearch,
          status: currentStatus // Kirim parameter status
        },
        dataType: 'json',
        success: function(response) {
          renderTable(response.data);
          renderPagination(response.pagination);
        },
        error: function() {
          $('#order-table-body').html('<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Gagal memuat data.</td></tr>');
          $('#pagination-info').text('Tidak ada data');
        }
      });
    }

    function renderTable(data) {
      const tableBody = $('#order-table-body');
      tableBody.empty();

      if (data.length === 0) {
        tableBody.html('<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data order ditemukan.</td></tr>');
        return;
      }

      data.forEach((order, index) => {
        // const editUrl = `{{ url('/project-officer/order') }}/${order.id_order_produksi}/edit`;

        const detailUrl = `{{ url('/project-officer/order') }}/${order.id_order_produksi}/detail`;

        const row = `
          <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary hover:underline">
              <a href="${detailUrl}">${escapeHTML(order.nomor_order)}</a>
            </td>
            <td class="px-6 py-4 ...">${escapeHTML(order.nama_sekolah)}</td>
            <td class="px-6 py-4 ...">${order.status_badge}</td>
            <td class="px-6 py-4 ...">
                <div>${escapeHTML(order.narahubung)}</div>
                <div class="text-xs text-gray-400">${escapeHTML(order.no_narahubung)}</div>
            </td>
            <td class="px-6 py-4 ...">${order.formatted_updated_at}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-base font-medium">
              <a href="${detailUrl}" class="text-green-600 hover:text-green-700">
                <ion-icon name="eye-outline"></ion-icon>
              </a>
            </td>
          </tr>
        `;
        tableBody.append(row);
      });
    }

    function renderPagination(pagination) {
      // ... (Kode Paginasi Lengkap dari jawaban Anda sebelumnya - tidak perlu diubah) ...
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

    // --- Event Listeners ---
    $('#search-input').on('keyup', debounce(function() {
      fetchData(1);
    }, 500));

    // Listener BARU untuk filter status
    $('#status-filter').on('change', function() {
      fetchData(1);
    });

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