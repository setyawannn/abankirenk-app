@extends('layouts.admin')

@section('title')
Template MoU
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <span>
    /
  </span>
  <span class="text-gray-600">
    Tambah Template MoU
  </span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <div class="card-df">
    {{-- PERBAIKAN: Action URL dan enctype untuk file upload --}}
    <form action="{{ url('/project-officer/template-mou/store') }}" method="POST" enctype="multipart/form-data">
      <div class="p-6 space-y-6">
        {{-- PERBAIKAN: Ganti field prospek dengan field template MoU --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-1">
            <label for="judul" class="label-df">
              Judul Template <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              name="judul"
              id="judul"
              class="input-df"
              placeholder="Contoh: MoU Paket Standar Sekolah A"
              required>
          </div>

          <div class="md:col-span-1">
            <label for="file_mou" class="label-df">
              File Template (Word/PDF) <span class="text-red-500">*</span>
            </label>
            <input
              type="file"
              name="file_mou"
              id="file_mou"
              class="input-file-df"
              accept=".doc, .docx, .pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf"
              required>
            <p class="desc-df">File harus berekstensi .doc, .docx, atau .pdf.</p>
          </div>

          <div class="col-span-2">
            <label for="deskripsi" class="label-df">
              Deskripsi
            </label>
            <textarea
              name="deskripsi"
              id="deskripsi"
              rows="4"
              class="input-df resize-none"
              placeholder="Contoh: Template ini digunakan untuk penawaran paket standar 64 halaman..."></textarea>
            <p class="desc-df">Tambahkan informasi singkat mengenai kegunaan template ini.</p>
          </div>
        </div>
      </div>

      <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
        <a
          href="{{ url('/project-officer/template-mou') }}"
          class="btn-outline-df">
          Batal
        </a>
        <button
          type="submit"
          class="btn-df">
          Simpan Template
        </button>
      </div>
    </form>
  </div>
</div>

@endsection