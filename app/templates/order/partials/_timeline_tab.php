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
@endphp

{{-- 
  Kartu Konten.
  Kita menggunakan $order['id_order_produksi'] (INT) untuk link 'create'.
  Kita menggunakan $order['nomor_order'] (VARCHAR) untuk ditampilkan.
--}}
<div class="card-df rounded-t-none">
    <div class="p-6">
        
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-medium">Timeline Produksi </h4>
            
            {{-- Tombol "Tambah" sekarang mengarah ke halaman 'create' --}}
            @if(in_array(auth()['role'], ['project_officer', 'manajer_produksi']))
            <a href="{{ url('/order/' . $order['id_order_produksi'] . '/timeline/create') }}" class="btn-df btn-sm">
                <ion-icon name="add"></ion-icon>
                Tambah Task
            </a>
            @endif
        </div>

        {{-- ========================================================== --}}
        {{--  PERBAIKAN: Loop 'columns' di luar, 'tasks' di dalam     --}}
        {{-- ========================================================== --}}
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            @foreach ($columns as $status => $style)
            <div class="kanban-column bg-gray-100 p-4 border border-gray-200 rounded-lg h-[80vh] space-y-6 overflow-y-auto">
                
                <div class="{{ $style['badge_color'] }} text-white px-4 py-1 w-fit text-sm rounded-full">
                    <span>{{ $status }}</span>
                </div>
                
                <div class="kanban-column-content flex flex-col gap-4 min-h-[50px]" data-status="{{ $status }}">
                    
                    {{-- Loop tasks HANYA untuk kolom ini --}}
                    @if (empty($tasks_by_status[$status]))
                        <p class="kanban-empty-message text-sm text-gray-400 p-4 text-center italic">Belum ada task.</p>
                    @endif
                    @foreach ($tasks_by_status[$status] as $item)
                    <div class="timeline-card bg-white p-4 rounded-md space-y-2 shadow-sm" 
                            data-task-id="{{ $item['id_timeline'] }}">
                        
                        <div class="flex justify-between items-center">
                            <h4 class="text-lg font-semibold text-gray-800">{{ $item['judul'] }}</h4>
                            {{-- Tombol Edit/Hapus --}}
                            @if(in_array(auth()['role'], ['project_officer', 'manajer_produksi']))
                            <div class="flex gap-2">
                                <a href="{{ url('/timeline/' . $item['id_timeline'] . '/edit') }}" class="text-primary hover:text-primary-700">
                                    <ion-icon name="create-outline"></ion-icon>
                                </a>
                                <form action="{{ url('/timeline/' . $item['id_timeline'] . '/delete') }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus task ini?');">
                                    <button type="submit" class="text-red-600 hover:text-red-700">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                        
                        {{-- Info Card --}}
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
        
    </div>
</div>

<style>
.timeline-card-placeholder {
    background-color: #e5e7eb;
    border: 2px dashed #9ca3af; 
    height: 120px; 
    border-radius: 0.375rem;
    margin-bottom: 1rem;
}

.dragging-card-helper {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transform: rotate(3deg); 
    cursor: grabbing !important; 
}
</style>

<script>
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

    // ==================================================
    //  FUNGSI KANBAN (DIPERBARUI TOTAL)
    // ==================================================
    function initKanbanTab() {
        
        // --- Helper untuk mengelola pesan "kosong" ---
        const emptyMessageHtml = '<p class="kanban-empty-message text-sm text-gray-400 p-4 text-center italic">Belum ada task.</p>';
        
        function updateKanbanEmptyMessages() {
            $(".kanban-column-content").each(function() {
                const $column = $(this);
                const $emptyMsg = $column.find('.kanban-empty-message');

                if ($column.find('.timeline-card').length === 0) {
                    // Kolom kosong, tambahkan pesan jika belum ada
                    if ($emptyMsg.length === 0) {
                        $column.append(emptyMessageHtml);
                    }
                } else {
                    // Kolom ada isinya, hapus pesan
                    if ($emptyMsg.length > 0) {
                        $emptyMsg.remove();
                    }
                }
            });
        }
        // --- Akhir Helper ---

        // Cek jika sudah diinisialisasi
        if ($(".kanban-column-content.ui-sortable").length > 0) {
            try {
                $(".kanban-column-content").sortable("destroy");
            } catch(e) {}
        }

        $(".kanban-column-content").sortable({
            connectWith: ".kanban-column-content",
            helper: "clone",
            appendTo: "body",
            tolerance: "intersect", // (Sesuai permintaan: 50%)
            placeholder: "timeline-card-placeholder", 
            
            start: function(event, ui) {
                ui.helper.addClass('dragging-card-helper');
                ui.helper.width(ui.item.width());
                
                // PERBAIKAN UX:
                // Cek kolom ASAL setelah card diangkat
                setTimeout(updateKanbanEmptyMessages, 50);
            },
            
            receive: function(event, ui) {
                const $card = $(ui.item);
                const $newColumn = $(this); 
                const taskId = $card.data('task-id');
                const newStatus = $newColumn.data('status');

                $card.css('opacity', 0.5);

                // PERBAIKAN UX:
                // Hapus pesan "kosong" dari kolom TUJUAN segera
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
                        // Cek status semua kolom setelah sukses
                        updateKanbanEmptyMessages();
                    },
                    error: function(xhr) {
                        $(ui.sender).sortable('cancel'); // Batalkan
                        $card.css('opacity', 1);
                        
                        // Cek status semua kolom setelah gagal/batal
                        updateKanbanEmptyMessages();
                        
                        const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal update status.';
                        if (window.showGlobalToast) {
                            showGlobalToast('error', 'Update Gagal', errorMsg);
                        }
                    }
                });
            }
        }).disableSelection();
    }
})();
</script>