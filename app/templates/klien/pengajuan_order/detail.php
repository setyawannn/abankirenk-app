@extends('layouts.admin')

@section('title')
Detail Pengajuan Order
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <span class="text-primary font-medium">
    Pengajuan Order
  </span>
  <span>
    /
  </span>
  <span class="text-gray-600">
    {{ $pengajuan['nomor_pengajuan'] }}
  </span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <div class="space-y-4">
    <div class="card-df">
      <div class="p-6 space-y-6">
        <h4 class="text-xl text-gray-800 font-semibold">Detail Pengajuan</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="txt-title-df">Nama Sekolah</label>
            <p class="txt-desc-df">{{ $pengajuan['nama_sekolah'] }}</p>
          </div>
          <div>
            <label class="txt-title-df">Dibuat</label>
            <p class="txt-desc-df">{{ $pengajuan['nama_klien'] }} | {{ $pengajuan['formatted_created_at'] }}</p>
          </div>
          <div>
            <label class="txt-title-df">Narahubung</label>
            <p class="txt-desc-df">{{ $pengajuan['narahubung'] }}</p>
          </div>
          <div>
            <label class="txt-title-df">No Narahubung</label>
            <p class="txt-desc-df">{{ $pengajuan['no_narahubung'] }}</p>
          </div>
          <div class="col-span-2">
            <label class="txt-title-df">Detail & Deskripsi</label>
            <div class="txt-editor-df">
              @if (empty($pengajuan['pesan']))
              <p class="italic text-gray-500">Belum ada pesan dari Project Officer.</p>
              @else
              {!! $pengajuan['pesan'] !!}
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-df">
      <div class="p-6 space-y-6">
        <h4 class="text-xl text-gray-800 font-semibold">Balasan Pengajuan</h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="txt-title-df">Staff Penanggung Jawab</label>
            <p class="txt-desc-df">{{ $pengajuan['nama_po'] ?? '-' }}</p>
          </div>
          <div>
            <label class="txt-title-df">Dibalas</label>
            <p class="txt-desc-df">{{ $pengajuan['formatted_tanggal_balasan'] ?? '-' }}</p>
          </div>
          <div>
            <label class="txt-title-df">Status Pengajuan</label>
            <p class="txt-desc-df">{!! $pengajuan['status_badge'] !!}</p>
          </div>
          <div class="col-span-2">
            <label class="label-df">Balasan</label>
            <div class="txt-editor-df">
              @if (empty($pengajuan['balasan']))
              <p class="italic text-gray-500">Belum ada balasan dari Project Officer.</p>
              @else
              {!! $pengajuan['balasan'] !!}
              @endif
            </div>
          </div>
        </div>


      </div>
    </div>

    <div class="flex justify-end">
      <a href="{{ url('/klien/pengajuan-order') }}" class="btn-outline-df">
        Kembali
      </a>
    </div>


  </div>
</div>
@endsection

@push('styles')
<style>
  .prose {
    color: #374151;
  }

  .prose p {
    margin-top: 0;
    margin-bottom: 1em;
  }

  .prose ul,
  .prose ol {
    margin-left: 1.25em;
  }

  .prose strong {
    font-weight: 600;
  }

  .prose em {
    font-style: italic;
  }
</style>
@endpush