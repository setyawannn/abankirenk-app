@extends('layouts.admin')

@section('title')
Detail Prospek
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <a href="{{ url('/manajer-marketing/manajemen-prospek') }}" class="text-primary hover:underline">Manajemen Prospek</a>
  <span>/</span>
  <span class="text-gray-600">Detail</span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <div class="w-full space-y-6">

    <div class="card-df">
      <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="txt-title-df">Narahubung</label>
            <p class="txt-desc-df">{{ $prospek['narahubung'] ?? $prospek['narahubung'] }} ( {{ $prospek['no_narahubung'] }})</p>
          </div>
          <div>
            <label class="txt-title-df">Nama Sekolah</label>
            <p class="txt-desc-df">{{ $sekolah['nama'] }}</p>
          </div>
          <div>
            <label class="txt-title-df">Staff Penanggung Jawab</label>
            <p class="txt-desc-df">{{ $staff['nama'] }} <span class="text-primary text-sm">({{ format_role_name($staff['role']) }})</span></p>
          </div>
          <div>
            <label class="txt-title-df">Status Prospek</label>
            <p class="txt-desc-df">{!! generate_status_badge($prospek['status_prospek']) !!} </p>
          </div>
          <div class="col-span-2">
            <label class="txt-title-df">Deskripsi Awal</label>
            <p style="font-size: 1rem" class="txt-desc-df">{{ $prospek['deskripsi'] ?? '-' }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="card-df">
      <div class="p-6 space-y-4">
        <div>
          <label class="txt-title-df mb-2 block">Catatan Tindak Lanjut</label>

          <div class="prose prose-sm max-w-none text-gray-700 p-4 border border-gray-200 rounded-lg bg-gray-50 min-h-[150px]">
            @if(empty($prospek['catatan']))
            <p class="italic text-gray-500">Belum ada catatan tindak lanjut dari staf marketing.</p>
            @else
            {!! $prospek['catatan'] !!}
            @endif
          </div>
        </div>

        <div class="flex justify-end mt-4">
          <a href="{{ url('/manajer-marketing/manajemen-prospek') }}" class="btn-outline-df">
            Kembali
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

{{-- Hapus @push('scripts') karena tidak butuh CKEditor --}}

@push('styles')
<style>
  /* Style sederhana untuk konten prose (hasil CKEditor) */
  .prose p {
    margin-top: 0;
    margin-bottom: 1em;
  }

  .prose ul {
    list-style-type: disc;
    padding-left: 1.5em;
    margin-bottom: 1em;
  }

  .prose ol {
    list-style-type: decimal;
    padding-left: 1.5em;
    margin-bottom: 1em;
  }

  .prose h1,
  .prose h2,
  .prose h3 {
    font-weight