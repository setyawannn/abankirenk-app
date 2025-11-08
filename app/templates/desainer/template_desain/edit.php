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
    Edit Template Desain
  </span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <div class="card-df">
    <form action="{{ url('/desainer/template-desain/' . $template['id_template_desain'] . '/update') }}" method="POST" enctype="multipart/form-data">
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
              value="{{ $template['judul'] }}"
              required>
          </div>
          
          <div class="md:col-span-1">
            <div class="flex justify-between items-center">
              <label for="file_desain" class="label-df">
                Upload File Baru
              </label>
              <div>
                <a
                  href="{{ url($template['template_desain']) }}" target="_blank"
                  class="text-sm text-white bg-primary px-2 py-1 rounded inline-flex items-center">
                  Preview File
                </a>
              </div>
            </div>
            <input
              type="file"
              name="file_desain"
              id="file_desain"
              class="input-file-df"
              accept="application/pdf">
            <p class="desc-df mt-2">Kosongkan jika tidak ingin mengubah file.</p>
          </div>

          <div class="md:col-span-2">
            <label for="deskripsi" class="label-df">
              Deskripsi
            </label>
            <textarea
              name="deskripsi"
              id="deskripsi"
              rows="4"
              class="input-df resize-none">{{ $template['deskripsi'] }}</textarea>
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
          Update Template
        </button>
      </div>
    </form>
  </div>
</div>
@endsection