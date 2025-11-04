@extends('layouts.admin')

@section('title')
Manajemen Prospek
@endsection

@section('content')
<div class="w-full mx-auto bg-white p-8 rounded-lg shadow-md">
    <form action="{{ url('manajer_marketing/prospek') }}" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="narahubung" class="block text-sm font-medium text-gray-700">Narahubung</label>
                <input type="text" name="narahubung" id="narahubung" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="e.g Sri Wahyuni">
            </div>
            <div>
                <label for="no_narahubung" class="block text-sm font-medium text-gray-700">Nomor Narahubung</label>
                <input type="text" name="no_narahubung" id="no_narahubung" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="e.g 081333717212">
            </div>
        </div>

        <div class="mt-6">
            <label for="id_sekolah" class="block text-sm font-medium text-gray-700">Nama Sekolah</label>
            <select name="id_sekolah" id="id_sekolah" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                <option value="">Pilih Nama Sekolah</option>
                @foreach($sekolah as $s)
                    <option value="{{ $s['id_sekolah'] }}">{{ $s['nama'] }}</option>
                @endforeach
            </select>
            <div class="mt-2">
                <input type="checkbox" name="sekolah_tidak_ada" id="sekolah_tidak_ada" class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-offset-0 focus:ring-primary focus:ring-opacity-50">
                <label for="sekolah_tidak_ada" class="ml-2 text-sm text-gray-600">Centang jika sekolah tidak ada pada pilihan</label>
            </div>
            <div id="input_sekolah_baru" class="mt-2 hidden">
                <input type="text" name="nama_sekolah_baru" id="nama_sekolah_baru" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="Masukkan nama sekolah baru">
            </div>
        </div>

        <div class="mt-6">
            <label for="id_user" class="block text-sm font-medium text-gray-700">Staf Penanggung Jawab (PIC)</label>
            <select name="id_user" id="id_user" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                <option value="">Pilih Staff</option>
                @foreach($staff as $st)
                    <option value="{{ $st['id_user'] }}">{{ $st['nama'] }}</option>
                @endforeach
            </select>
            <p class="mt-2 text-sm text-gray-500">Pilih tim marketing yang menjadi penanggung jawab</p>
        </div>

        <div class="mt-6">
            <label for="catatan" class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="catatan" id="catatan" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="e.g Deskripsi client request ataupun kriteria dan karakter"></textarea>
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ url('manajer_marketing/prospek') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">Batal</a>
            <button type="submit" class="bg-primary hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('sekolah_tidak_ada').addEventListener('change', function() {
        var inputSekolahBaru = document.getElementById('input_sekolah_baru');
        if (this.checked) {
            inputSekolahBaru.classList.remove('hidden');
        } else {
            inputSekolahBaru.classList.add('hidden');
        }
    });
</script>
@endpush