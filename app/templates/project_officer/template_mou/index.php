<!-- templates/project_officer/template_mou/index.php -->
@extends('layouts.admin')

@section('title')
Template MoU
@endsection

@section('content')
<div class="space-y-6">
  <div class="rounded-lg flex items-center space-x-4">
    <div class="flex-grow">
      <label for="search-template" class="sr-only">Cari Template</label>
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <ion-icon name="search-outline" class="text-gray-400"></ion-icon>
        </div>
        <input type="text" id="search-template" class="block w-[300px] pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Cari judul atau deskripsi...">
      </div>
    </div>

    <a href="{{ url('/project-officer/template-mou/create') }}" class="btn-df">
      <ion-icon name="add-outline" class="-ml-1 mr-2"></ion-icon>
      Tambah Template
    </a>
  </div>

  <div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diperbarui</th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">Aksi</span>
            </th>
          </tr>
        </thead>
        <tbody id="template-table-body" class="bg-white divide-y divide-gray-200">
          <tr>
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Memuat data...</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 flex items-center justify-between sm:px-6 rounded-b-lg border-t border-gray-200">
      <div class="flex-1 flex justify-between sm:hidden">
        <button id="prev-mobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50" disabled> Previous </button>
        <button id="next-mobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50" disabled> Next </button>
      </div>
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
<div
  id="modal-konfirmasi-hapus"
  class="modal-container fixed inset-0 z-40 p-4
           flex items-center justify-center 
           invisible">
  <div class="modal-overlay fixed inset-0 bg-gray-900/50 backdrop-blur-sm
                opacity-0 transition-opacity duration-300 ease-out">
  </div>

  <div class="modal-box relative z-50 w-full max-w-md rounded-lg bg-white p-6 shadow-xl 
                opacity-0 scale-95 transition-all duration-300 ease-out">
    <form id="form-delete-modal" action="" method="POST">
      <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold text-gray-900">
          Konfirmasi Hapus
        </h3>
        <button
          type="button"
          data-modal-dismiss="#modal-konfirmasi-hapus"
          class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
          <ion-icon name="close" class="h-6 w-6"></ion-icon>
        </button>
      </div>
      <div class="mt-4">
        <p class="text-gray-600">
          Apakah Anda yakin ingin menghapus <strong>template</strong> ini? Tindakan ini tidak dapat dibatalkan.
        </p>
      </div>
      <div class="mt-6 flex justify-end gap-3">
        <button
          type="button"
          data-modal-dismiss="#modal-konfirmasi-hapus"
          class="btn-outline-df">
          Batal
        </button>
        <button
          type="submit"
          class="btn-danger-df">
          Ya, Hapus
        </button>
      </div>
    </form>
  </div>
</div>
@endpush

@push('scripts')
<script>
  $(document).ready(function() {
    let currentPage = 1;
    let currentSearch = '';
    const limit = 10;

    function fetchData(page = 1) {
      currentPage = page;
      currentSearch = $('#search-template').val();

      $('#template-table-body').html('<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Memuat data...</td></tr>');
      $('#pagination-controls').empty();
      $('#prev-mobile, #next-mobile').prop('disabled', true);

      $.ajax({
        url: "{{ url('/ajax/template-mou') }}",
        type: 'GET',
        data: {
          page: currentPage,
          limit: limit,
          search: currentSearch,
        },
        dataType: 'json',
        success: function(response) {
          renderTable(response.data);
          renderPagination(response.pagination);
        },
        error: function() {
          $('#template-table-body').html('<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Gagal memuat data. Silakan coba lagi.</td></tr>');
          $('#pagination-info').text('Tidak ada data');
        }
      });
    }

    function renderTable(data) {
      const tableBody = $('#template-table-body');
      tableBody.empty();

      if (data.length === 0) {
        tableBody.html('<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data template ditemukan.</td></tr>');
        return;
      }

      data.forEach((template, index) => {

        const editUrl = `{{ url('/project-officer/template-mou') }}/${template.id_template_mou}/edit`;
        const deleteUrl = `{{ url('/project-officer/template-mou') }}/${template.id_template_mou}/destroy`;
        const fileName = template.mou ? template.mou.split('/').pop() : 'N/A';

        const row = `
          <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${escapeHTML(template.judul)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <a href="{{ url('') }}${escapeHTML(template.mou)}" target="_blank" class="text-primary hover:underline flex items-center gap-2">
                   <ion-icon name="document-text-outline"></ion-icon>
                   ${escapeHTML(fileName)}
                </a>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">${escapeHTML(template.deskripsi || '-')}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${template.formatted_updated_at}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-base font-medium">
              <a href="${editUrl}" class="text-primary hover:text-primary-700">
                <ion-icon name="create-outline"></ion-icon>
              </a>
              <button
                type="button"
                data-modal-target="#modal-konfirmasi-hapus"
                data-url="${deleteUrl}"
                class="text-red-600 hover:text-red-800 ml-4 cursor-pointer open-delete-modal">
                <ion-icon name="trash-outline"></ion-icon>
              </button>
            </td>
          </tr>
        `;
        tableBody.append(row);
      });
    }

    $(document).on('click', '.open-delete-modal', function() {
      const deleteUrl = $(this).data('url');
      $('#form-delete-modal').attr('action', deleteUrl);
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

      const from = total > 0 ? (current_page - 1) * per_page + 1 : 0;
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

    $('#search-template').on('keyup', debounce(function() {
      fetchData(1);
    }, 500));

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