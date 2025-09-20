@extends('layouts.app')

@section('title')
Dashboard
@endsection

@section('content')
<div class="bg-gray-100 min-h-screen">
  <div class="container mx-auto px-4 py-8">

    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Selamat Datang, {{ $user['name'] }}!</h1>
      <p class="text-gray-600">Anda login sebagai:
        <span class="font-semibold px-2 py-1 text-sm rounded
                    @if ($user['role'] === 'admin')
                        bg-red-200 text-red-800
                    @else
                        bg-blue-200 text-blue-800
                    @endif
                ">
          {{ ucfirst($user['role']) }}
        </span>
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

      @if ($user['role'] === 'admin')
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Statistik Admin</h2>
        <div class="space-y-3">
          <p><strong>Total Pengguna:</strong> {{ $admin_stats['total_users'] }}</p>
          <p><strong>Status Server:</strong>
            <span class="text-green-600 font-bold">{{ $admin_stats['server_status'] }}</span>
          </p>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Aksi Admin</h2>
        <p class="mb-4">Kelola sistem dari sini.</p>
        <a href="{{ url('/admin/settings') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
          Buka Pengaturan Admin
        </a>
      </div>
      @endif

      @if ($user['role'] === 'user')
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Notifikasi Anda</h2>
        <div class="space-y-3">
          <p><strong>Pesan Belum Dibaca:</strong> {{ $user_stats['unread_messages'] }}</p>
          <p><strong>Login Terakhir:</strong> {{ $user_stats['last_login'] }}</p>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Profil Saya</h2>
        <p class="mb-4">Lihat atau perbarui informasi profil Anda.</p>
        <a href="#" class="inline-block bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
          Lihat Profil (segera)
        </a>
      </div>
      @endif

      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Logout</h2>
        <p class="mb-4">Keluar dari sesi Anda saat ini.</p>
        <a href="{{ url('/logout') }}" class="inline-block bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
          Logout
        </a>
      </div>

    </div>

  </div>
</div>
@endsection

@push('scripts')

@endpush