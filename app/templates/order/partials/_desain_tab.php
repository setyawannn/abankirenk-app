{{-- File ini dimuat oleh AJAX --}}
<div class="card-df rounded-t-none">
  <div class="p-6">

    <h4 class="text-lg font-medium mb-4">Desain</h4>
    @if ($can_upload && auth()['role'] === 'desainer')
    <div class="card-df border border-gray-200 mb-6">
      <div class="p-6">
        <form action="{{ url('/order/' . $order['id_order_produksi'] . '/desain/store') }}" method="POST" enctype="multipart/form-data">
          <div class="w-full space-y-4">
            <div class="w-full">
              <label for="file_desain" class="label-df">
                Upload File Desain (PDF/ZIP/PSD/AI)
              </label>
              <input
                type="file"
                name="file_desain"
                id="file_desain"
                class="input-file-df"
                accept=".pdf,.zip,.rar,.psd,.ai"
                required>
              <p class="desc-df">Mengupload file baru akan menambahkannya ke riwayat revisi.</p>
            </div>
            <div class="w-full flex justify-end">
              <button
                type="submit"
                class="btn-df">
                <ion-icon name="cloud-upload-outline" class="mr-2"></ion-icon>
                Upload
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
    @endif

    <div class="space-y-4">
      <h5 class="text-base font-medium">Pratinjau Desain Terbaru</h5>
      <div class="card-df border border-gray-200">
        <div class="p-6">
          @if (empty($latest_design))
          <p class="text-gray-500 text-center p-4">File Desain untuk order ini belum di-upload.</p>
          @else
          @php
          $fileUrl = $latest_design['desain'];
          $extension = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
          @endphp

          @if ($extension == 'pdf')
          <iframe
            src="{{ url($fileUrl) }}"
            class="w-full h-[600px] rounded-md border border-gray-300"
            frameborder="0">
          </iframe>
          @else
          <div class="text-center p-8 bg-gray-50 rounded-md">
            <ion-icon name="document-zip-outline" class="text-5xl text-gray-400"></ion-icon>
            <p class="mt-2 text-gray-600">Pratinjau tidak tersedia untuk file ini.</p>
            <a href="{{ url($fileUrl) }}" target="_blank" download class="btn-df mt-4">
              Download File ({{ $extension }})
            </a>
          </div>
          @endif

          <div class="mt-4 text-sm text-gray-500">
            Di-upload oleh: <span class="font-medium">{{ $latest_design['nama_uploader'] ?? 'N/A' }}</span>
          </div>
          @endif
        </div>
      </div>
    </div>

    @if (!empty($design_history))
    <div class="space-y-4 mt-6">
      <h5 class="text-base font-medium">Riwayat Revisi</h5>
      <ul class="divide-y divide-gray-200 border rounded-md border-gray-300">
        @foreach ($design_history as $history)
        <li class="p-3 flex justify-between items-center hover:bg-gray-50 w-full rounded-md">
          <div>
            <p class="text-sm font-medium text-gray-900">
              {{ $history['desain'] ? basename($history['desain']) : 'File tidak diketahui' }}
            </p>
            <p class="text-xs text-gray-500">
              Di-upload oleh {{ $history['nama_uploader'] ?? 'N/A' }}
              pada {{ date('d/m/y H:i', strtotime($history['created_at'])) }}
            </p>
          </div>
          <a href="{{ url($history['desain']) }}" target="_blank" class="btn-outline-df btn-sm">
            Lihat
          </a>
        </li>
        @endforeach
      </ul>
    </div>
    @endif

  </div>
</div>