@extends('layouts.app')

@section('title')
Register
@endsection

@section('content')

@php
$errorMessage = flash_message('error');
@endphp

<div class="flex items-center justify-center min-h-screen bg-gray-100">
  <div class="px-8 py-6 mt-4 text-left bg-white shadow-lg rounded-lg">
    <h3 class="text-2xl font-bold text-center">Buat Akun Baru</h3>

    @if ($errorMessage)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-4" role="alert">
      {{ $errorMessage }}
    </div>
    @endif

    <form action="{{ url('register') }}" method="POST">
      <div class="mt-4">
        <div>
          <label class="block" for="full_name">Nama Lengkap<label>
              <input type="text" placeholder="Nama Lengkap" name="full_name"
                class="w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600">
        </div>
        <div class="mt-4">
          <label class="block" for="email">Email<label>
              <input type="email" placeholder="Email" name="email"
                class="w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600">
        </div>
        <div class="mt-4">
          <label class="block" for="phone_number">No. HP (Opsional)<label>
              <input type="text" placeholder="No. HP" name="phone_number"
                class="w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600">
        </div>
        <div class="mt-4">
          <label class="block">Password<label>
              <input type="password" placeholder="Password" name="password"
                class="w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600">
        </div>
        <div class="flex items-baseline justify-between">
          <button class="px-6 py-2 mt-4 text-white bg-blue-600 rounded-lg hover:bg-blue-900">Register</button>
          <a href="{{ url('/login') }}" class="text-sm text-blue-600 hover:underline">Sudah punya akun?</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection