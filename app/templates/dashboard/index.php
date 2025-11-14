@extends('layouts.admin')

@section('title')
Dashboard
@endsection

@section('content')
<div class="space-y-6">
  <h2 class="text-2xl font-semibold text-gray-800">Selamat Datang Kembali!</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-sm">
      <h3 class="text-lg font-medium text-gray-600">Total Siswa</h3>
      <p class="text-3xl font-bold text-gray-900 mt-2">1,250</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm">
      <h3 class="text-lg font-medium text-gray-600">Sesi Hari Ini</h3>
      <p class="text-3xl font-bold text-gray-900 mt-2">12</p>
    </div>
  </div>
</div>
@endsection