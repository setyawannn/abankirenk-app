@extends('layouts.admin')

@section('title')
Template Desain
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <span>
    /
  </span>
  <span class="text-gray-600">
    Tambah Template Desain
  </span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <div class="card-df">
    <form action="{{ url('/desainer/template-desain/store') }}" method="POST" enctype="multipart/form-data">
      <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-1">
            <label for="judul" class="label-df">
              Judul Desain <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              name="judul"
              id="judul"
              class="input-df"
              placeholder="Contoh: Desain Tema Vintage 01"
              required>
          </div>

          <div class="md:col-span-1">
            <label for="file_desain" class="label-df">
              File Gambar (PDF) <span class="text-red-500">*</span>
            </label>
            <input
              type="file"
              name="file_desain"
              id="file_desain"
              class="input-file-df"
              accept="application/pdf"
              required>
          </div>

          <div class="md:col-span-2">
            <label for="deskripsi" class="label-df">
              Deskripsi
            </label>
            <textarea
              name="deskripsi"
              id="deskripsi"
              rows="4"
              class="input-df resize-none"
              placeholder="Contoh: Tema vintage dengan layout bersih, cocok untuk..."></textarea>
            <p class="desc-df">Tambahkan informasi singkat mengenai kegunaan template ini.</p>
          </div>
        </div>
      </div>

      <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
        <a
          href="{{ url('/desainer/template-desain') }}"
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