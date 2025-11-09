@extends('layouts.admin')

@section('title')
Buat Order Produksi Baru
@endsection

@section('breadcrumbs')
<div class="flex gap-2 items-center">
  <span>
    /
  </span>
  <span class="text-gray-600">
    Buat Order Baru
  </span>
</div>
@endsection

@section('content')
<div class="w-full mx-auto">
  <div class="card-df">
    <form action="{{ url('/project-officer/order/store') }}" method="POST" enctype="multipart/form-data">

      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900 mb-4">1. Sumber Order</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="col-span-2">
            <label for="sumber_order" class="label-df">
              Pilih Sumber (dari Prospek/Pengajuan Berhasil) <span class="text-red-500">*</span>
            </label>
            <select id="sumber_order" name="sumber_order" class="input-df" required>
              <option value="">Pilih sumber order...</option>
              @foreach($sumber_order as $sumber)
              <option value="{{ $sumber['source_type'] }}_{{ $sumber['source_id'] }}">
                {{ $sumber['nama_sekolah'] }} ({{ ucfirst($sumber['source_type']) }} - {{ $sumber['narahubung'] }})
              </option>
              @endforeach
            </select>
          </div>

          <div>
            <label for="narahubung" class="label-df">Narahubung</label>
            <input type="text" name="narahubung" id="narahubung" class="input-df bg-gray-100" readonly required>
          </div>
          <div>
            <label for="no_narahubung" class="label-df">No. Narahubung</label>
            <input type="text" name="no_narahubung" id="no_narahubung" class="input-df bg-gray-100" readonly required>
          </div>

          <div class="col-span-2">
            <label for="nama_sekolah" class="label-df">Sekolah</label>
            <input type="text" id="nama_sekolah" class="input-df bg-gray-100" readonly required>
            <input type="hidden" name="id_sekolah" id="id_sekolah">
          </div>
        </div>
      </div>

      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900 mb-4">2. Akun Klien</h3>
        <div class="space-y-6">
          <div>
            <label for="id_klien" class="label-df">
              Pilih Akun Klien <span class="text-red-500">*</span>
            </label>
            <select id="id_klien" name="id_klien" class="input-df" required>
              <option value="">Pilih klien...</option>
              @foreach($klien_list as $klien)
              <option value="{{ $klien['id_user'] }}">{{ $klien['nama'] }} ({{ $klien['email'] }})</option>
              @endforeach
            </select>
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" name="create_klien_checkbox" id="create_klien_checkbox">
            <p style="margin-top: 0;" class="desc-df">Centang jika akun klien belum dibuat.</p>
          </div>

          <div id="new-klien-fields" class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 border border-gray-300 rounded-lg bg-gray-50" style="display: none;">
            <div>
              <label for="nama_klien" class="label-df">Nama Klien Baru <span class="text-red-500">*</span></label>
              <input type="text" name="nama_klien" id="nama_klien" class="input-df" required disabled>
            </div>
            <div>
              <label for="email_klien" class="label-df">Email Klien Baru <span class="text-red-500">*</span></label>
              <input type="email" name="email_klien" id="email_klien" class="input-df" required disabled>
            </div>
            <div>
              <label for="username_klien" class="label-df">Username Klien Baru <span class="text-red-500">*</span></label>
              <input type="text" name="username_klien" id="username_klien" class="input-df" required disabled>
            </div>
            <div class="col-span-3">
              <p class="desc-df">Password default akan diatur ke: <strong>12345678</strong>. Klien dapat mengubahnya setelah login.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">3. Detail Order</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="kuantitas" class="label-df">Kuantitas Cetak <span class="text-red-500">*</span></label>
            <input type="number" name="kuantitas" id="kuantitas" class="input-df" placeholder="Contoh: 150" required>
          </div>
          <div>
            <label for="halaman" class="label-df">Jumlah Halaman <span class="text-red-500">*</span></label>
            <input type="number" name="halaman" id="halaman" class="input-df" placeholder="Contoh: 64" required>
          </div>
          <div>
            <label for="deadline" class="label-df">Tenggat Waktu (Deadline) <span class="text-red-500">*</span></label>
            <input type="date" name="deadline" id="deadline" class="input-df" required>
          </div>
          <div>
            <label for="file_mou" class="label-df">Upload File MoU <span class="text-red-500">*</span></label>
            <input type="file" name="file_mou" id="file_mou" class="input-file-df" required>
          </div>
          <div class="col-span-2">
            <label for="konsep" class="label-df">Konsep / Tema</label>
            <textarea name="konsep" id="konsep" rows="4" class="input-df resize-none" placeholder="Tulis catatan mengenai konsep, tema, atau permintaan khusus..."></textarea>
          </div>
        </div>
      </div>

      <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
        <a
          href="{{ url('/project-officer/order') }}"
          class="btn-outline-df">
          Batal
        </a>
        <button
          type="submit"
          class="btn-df">
          Simpan Order
        </button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<link href="{{ url('/css/tom-select.css') }} " rel="stylesheet">
<script src="{{ url('/js/tom-select.js') }}"></script>

<script>
  $(document).ready(function() {
    const tomSelectSumber = new TomSelect('#sumber_order', {
      placeholder: 'Ketik untuk mencari sekolah atau narahubung...',
    });

    const tomSelectKlien = new TomSelect('#id_klien', {
      placeholder: 'Ketik untuk mencari klien...',
    });

    $('#sumber_order').on('change', function() {
      const sourceKey = $(this).val();

      if (!sourceKey) {
        $('#narahubung').val('').prop('readonly', true);
        $('#no_narahubung').val('').prop('readonly', true);
        $('#nama_sekolah').val('').prop('readonly', true);
        $('#id_sekolah').val('');
        tomSelectKlien.clear();
        return;
      }

      $('#narahubung').val('Memuat...').prop('readonly', true);
      $('#no_narahubung').val('Memuat...').prop('readonly', true);

      const selectedText = tomSelectSumber.options[sourceKey].text;
      const namaSekolah = selectedText.split('(')[0].trim();
      $('#nama_sekolah').val(namaSekolah).prop('readonly', true);

      $.ajax({
        url: '{{ url("/ajax/po/get-source-details") }}',
        type: 'GET',
        data: {
          source_key: sourceKey
        },
        success: function(response) {
          $('#narahubung').val(response.narahubung).prop('readonly', true);
          $('#no_narahubung').val(response.no_narahubung).prop('readonly', true);
          $('#id_sekolah').val(response.id_sekolah);

          if (response.id_klien_existing) {
            tomSelectKlien.setValue(response.id_klien_existing);
          } else {
            tomSelectKlien.clear();
          }
        },
        error: function() {
          alert('Gagal mengambil detail sumber. Silakan coba lagi.');
          $('#narahubung').val('').prop('readonly', true);
          $('#no_narahubung').val('').prop('readonly', true);
        }
      });
    });

    const $newKlienFields = $('#new-klien-fields');
    const $newKlienInputs = $newKlienFields.find('input');
    const $klienSelectInput = $('#id_klien');

    $('#create_klien_checkbox').change(function() {
      if (this.checked) {
        $newKlienFields.show();
        $newKlienInputs.prop('disabled', false);

        tomSelectKlien.disable();
        tomSelectKlien.clear();
        $klienSelectInput.prop('required', false);

      } else {
        $newKlienFields.hide();
        $newKlienInputs.prop('disabled', true);

        tomSelectKlien.enable();
        $klienSelectInput.prop('required', true);
      }
    });

    $('#kuantitas, #halaman').on('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });

  });
</script>
@endpush