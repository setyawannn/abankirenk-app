@extends('layouts.auth')

@section('title')
Login
@endsection

@section('content')

<div class="flex flex-col items-center justify-center min-h-screen px-4 py-12">

  <div class="flex items-center gap-2 mb-6">
    <ion-icon name="book-outline" class="text-3xl text-primary"></ion-icon>
    <h1 class="text-3xl font-bold text-gray-800">AbankIrenk</h1>
  </div>

  <div class="w-full max-w-md bg-white shadow-xl rounded-lg overflow-hidden">
    <div class="p-8">
      <h2 class="text-2xl font-bold text-center text-gray-900">Login ke Akun Anda</h2>

      <form action="{{ url('/login') }}" method="POST" class="mt-6 space-y-4">
        <div>
          <label for="email" class="label-df">Email</label>
          <input
            type="email"
            placeholder="Email"
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

        <div class="text-right">
          <a href="#" class="text-sm font-medium text-primary hover:underline">Lupa Password?</a>
        </div>

        <div>
          <button type="submit" class="btn-df w-full flex justify-center items-center">Login</button>
        </div>
      </form>
    </div>

    <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
      <p class="text-sm text-gray-600">
        Belum punya akun?
        <a href="{{ url('/register') }}" class="font-medium text-primary hover:underline">Buat akun</a>
      </p>
    </div>
  </div>
</div>

@endsection