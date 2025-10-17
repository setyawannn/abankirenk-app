@extends('layouts.app')

@section('title')
Login
@endsection

@section('content')

@php
$successMessage = flash_message('success');
$errorMessage = flash_message('error');
@endphp

<div class="flex items-center justify-center min-h-screen bg-gray-100">
  <div class="px-8 py-6 mt-4 text-left bg-white shadow-lg rounded-lg">
    <h3 class="text-2xl font-bold text-center">Login ke Akun Anda</h3>

    @if ($successMessage)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative my-4" role="alert">
      {{ $successMessage }}
    </div>
    @endif
    @if ($errorMessage)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-4" role="alert">
      {{ $errorMessage }}
    </div>
    @endif

    <form action="{{url('/login')}}" method="POST">
      <div class="mt-4">
        <div>
          <label class="block" for="email">Email<label>
              <input type="email" placeholder="Email" name="email"
                class="w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600">
        </div>
        <div class="mt-4">
          <label class="block">Password<label>
              <input type="password" placeholder="Password" name="password"
                class="w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600">
        </div>
        <div class="flex items-baseline justify-between">
          <button class="px-6 py-2 mt-4 text-white bg-blue-600 rounded-lg hover:bg-blue-900">Login</button>
          <a href="{{url('/register')}}" class="text-sm text-blue-600 hover:underline">Buat akun</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection