{{-- templates/order/partials/_timeline_tab.php --}}
@php
// Tentukan kolom Kanban kita
$columns = [
'Ditugaskan' => ['badge_color' => 'bg-yellow-500'],
'Dalam Proses' => ['badge_color' => 'bg-orange-500'],
'Selesai' => ['badge_color' => 'bg-green-600']
];

// Buat "ember" kosong untuk setiap status
$tasks_by_status = [
'Ditugaskan' => [],
'Dalam Proses' => [],
'Selesai' => []
];

// Masukkan $items (dari action) ke "ember" yang benar
if (!empty($items)) {
foreach ($items as $item) {
if (isset($tasks_by_status[$item['status_timeline']])) {
$tasks_by_status[$item['status_timeline']][] = $item;
}
}
}

$isAdminRole = in_array(auth()['role'], ['project_officer', 'manajer_produksi']);
@endphp

<div class="card-df rounded-t-none">
    <div class="p-6">

        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-medium">Timeline Produksi</h4>
            @if(in_array(auth()['role'], ['project_officer', 'manajer_produksi']))
            <a href="{{ url('/order/' . $order['id_order_produksi'] . '/timeline/create') }}" class="btn-df btn-sm">
                <ion-icon name="add"></ion-icon>
                Tambah Task
            </a>
            @endif
        </div>

        @if (empty($items))
        <p class="text-gray-500 text-center p-4 col-span-3">Belum ada timeline yang dibuat.</p>
        @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            @foreach ($columns as $status => $style)
            <div class="kanban-column bg-gray-100 p-4 border border-gray-200 rounded-lg h-[80vh] space-y-6 overflow-y-auto">

                <div class="{{ $style['badge_color'] }} text-white px-4 py-1 w-fit text-sm rounded-full">
                    <span>{{ $status }}</span>
                </div>

                <div class="kanban-column-content flex flex-col gap-4 min-h-[50px]" data-status="{{ $status }}">

                    @if (empty($tasks_by_status[$status]))
                    <p class="kanban-empty-message text-sm text-gray-400 p-4 text-center italic">Belum ada task.</p>
                    @endif

                    @foreach ($tasks_by_status[$status] as $item)
                    <div class="timeline-card bg-white p-4 rounded-md space-y-2 cursor-move shadow-sm hover:shadow-md transition-shadow"
                        data-task-id="{{ $item['id_timeline'] }}">

                        <div class="flex justify-between items-center">
                            @php
                            $taskUrl = $isAdminRole
                            ? url('/timeline/' . $item['id_timeline'] . '/edit')
                            : url('/timeline/' . $item['id_timeline'] . '/detail');
                            @endphp
                            <a href="{{ $taskUrl }}"
                                class="text-lg font-semibold text-gray-800 kanban-card-title hover:text-primary hover:underline">
                                {{ $item['judul'] }}
                            </a>
                            @if(in_array(auth()['role'], ['project_officer', 'manajer_produksi']))
                            @if($isAdminRole)
                            <div class="flex gap-2 pl-2 flex-shrink-0">
                                <a href="{{ url('/timeline/' . $item['id_timeline'] . '/edit') }}" class="text-primary">
                                    <ion-icon name="create-outline"></ion-icon>
                                </a>
                                <button type="button"
                                    class="text-red-600 open-delete-modal cursor-pointer"
                                    data-modal-target="#modal-konfirmasi-hapus"
                                    data-url="{{ url('/timeline/' . $item['id_timeline'] . '/delete') }}">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </div>
                            @endif
                            @endif
                        </div>

                        <div class="flex flex-col gap-2">
                            <div class="text-base text-gray-500 space-x-1">
                                <ion-icon name="person-outline" class="text-primary"></ion-icon>
                                <span>{{ $item['nama_user'] ?? 'N/A' }}</span>
                            </div>
                            <div class="text-base text-gray-500 space-x-1">
                                <ion-icon name="calendar-clear-outline" class="text-primary"></ion-icon>
                                <span>{{ date('d/m/Y', strtotime($item['deadline'])) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- ========================================================== --}}
{{-- STYLE & SCRIPT KHUSUS KANBAN                            --}}
{{-- ========================================================== --}}
<style>
    .kanban-card-title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        /* Tentukan tinggi 2 baris agar layout stabil */
        min-height: 3.5rem;
        /* (1.75rem * 2) */
        line-height: 1.75rem;
        /* (sesuai text-lg) */
        word-break: break-word;
        /* Memecah kata jika 1 kata terlalu panjang */
    }

    /* 2. Placeholder (Slot kosong) */
    .timeline-card-placeholder {
        background-color: #e5e7eb;
        border: 2px dashed #9ca3af;
        height: 120px;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
        cursor: grabbing !important;
    }

    /* 3. Helper (Bayangan yang diseret) */
    .dragging-card-helper {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: rotate(3deg);
        cursor: grabbing !important;
        max-width: 20rem;
    }
</style>

<script>
    // Panggil fungsi inisialisasi
    (function() {
        try {
            if (typeof $.fn.sortable === 'function') {
                initKanbanTab();
            } else {
                console.error("jQuery UI Sortable tidak dimuat.");
            }
        } catch (e) {
            console.error("Gagal inisialisasi Kanban:", e);
        }

        function initKanbanTab() {

            // --- Helper untuk mengelola pesan "kosong" ---
            const emptyMessageHtml = '<p class="kanban-empty-message text-sm text-gray-400 p-4 text-center italic">Belum ada task.</p>';

            function updateKanbanEmptyMessages() {
                $(".kanban-column-content").each(function() {
                    const $column = $(this);
                    const $emptyMsg = $column.find('.kanban-empty-message');
                    if ($column.find('.timeline-card').length === 0) {
                        if ($emptyMsg.length === 0) {
                            $column.append(emptyMessageHtml);
                        }
                    } else {
                        if ($emptyMsg.length > 0) {
                            $emptyMsg.remove();
                        }
                    }
                });
            }
            // --- Akhir Helper ---

            if ($(".kanban-column-content.ui-sortable").length > 0) {
                try {
                    $(".kanban-column-content").sortable("destroy");
                } catch (e) {}
            }

            $(".kanban-column-content").sortable({
                connectWith: ".kanban-column-content",
                helper: "clone",
                appendTo: "body",
                tolerance: "intersect",
                placeholder: "timeline-card-placeholder",


                start: function(event, ui) {
                    ui.helper.addClass('dragging-card-helper');

                    // 2. 'outerWidth()' & 'outerHeight()'
                    //    Memaksa "bayangan" memiliki ukuran yang SAMA PERSIS
                    //    dengan card aslinya, termasuk padding/border.
                    ui.helper.outerWidth(ui.item.outerWidth());
                    ui.helper.outerHeight(ui.item.outerHeight());

                    setTimeout(updateKanbanEmptyMessages, 50);
                },

                receive: function(event, ui) {
                    const $card = $(ui.item);
                    const $newColumn = $(this);
                    const taskId = $card.data('task-id');
                    const newStatus = $newColumn.data('status');

                    $card.css('opacity', 0.5);
                    $newColumn.find('.kanban-empty-message').remove();

                    $.ajax({
                        url: '{{ url("/ajax/timeline/update-status") }}',
                        type: 'POST',
                        data: {
                            id_timeline: taskId,
                            status_timeline: newStatus
                        },
                        success: function(response) {
                            $card.css('opacity', 1);
                            if (window.showGlobalToast) {
                                showGlobalToast('success', 'Status Diperbarui', response.message);
                            }
                            updateKanbanEmptyMessages(); // Cek semua kolom
                        },
                        error: function(xhr) {
                            $(ui.sender).sortable('cancel');
                            $card.css('opacity', 1);
                            updateKanbanEmptyMessages(); // Cek semua kolom

                            const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal update status.';
                            if (window.showGlobalToast) {
                                showGlobalToast('error', 'Update Gagal', errorMsg);
                            }
                        }
                    });
                }
            }).disableSelection();
        }

        $(document).off('click', '.open-delete-modal').on('click', '.open-delete-modal', function() {
            const deleteUrl = $(this).data('url');
            $('#form-delete-modal').attr('action', deleteUrl);

            $('#modal-konfirmasi-hapus .modal-box p').text('Anda yakin ingin menghapus task timeline ini?');

            showModal('#modal-konfirmasi-hapus');
        });
    })();
</script>