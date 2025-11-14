@extends('layouts.auth')

@section('title')
Register
@endsection

@section('content')

<div class="flex flex-col items-center justify-center min-h-screen px-4 py-12">

  <div class="flex items-center gap-2 mb-6">
    <ion-icon name="book-outline" class="text-3xl text-primary"></ion-icon>
    <h1 class="text-3xl font-bold text-gray-800">AbankIrenk</h1>
  </div>

  <div class="w-full max-w-md bg-white shadow-xl rounded-lg overflow-hidden">
    <div class="p-8">
      <h2 class="text-2xl font-bold text-center text-gray-900">Buat Akun Baru</h2>
      <p class="text-center text-sm text-gray-500 mt-1">Daftar untuk mulai mengelola order Anda.</p>

      <form action="{{ url('/register') }}" method="POST" class="mt-6 space-y-4">
        <div>
          <label for="nama" class="label-df">Nama Lengkap</label>
          <input
            type="text"
            placeholder="Nama Lengkap Anda"
            name="nama"
            id="nama"
            class="input-df"
            required>
        </div>

        <div>
          <label for="username" class="label-df">Username</label>
          <input
            type="text"
            placeholder="Username (tanpa spasi)"
            name="username"
            id="username"
            class="input-df"
            required>
        </div>

        <div>
          <label for="email" class="label-df">Email</label>
          <input
            type="email"
            placeholder="Email Anda"
            name="email"
            id="email"
            class="input-df"
            required>
        </div>

        <div>
          <label for="password" class="label-df">Password</label>
          <input
            type="password"
            placeholder="Password"
            name="password"
            id="password"
            class="input-df"
            required>
        </div>

        <div>
          <button type="submit" class="btn-df w-full flex justify-center items-center">Register</button>
        </div>
      </form>
    </div>

    <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
      <p class="text-sm text-gray-600">
        Sudah punya akun?
        <a href="{{ url('/login') }}" class="font-medium text-primary hover:underline">Login di sini</a>
      </p>
    </div>
  </div>
</div>

@endsection