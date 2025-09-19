<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>{{ config('app.name') }} - @yield('title', 'Selamat Datang')</title>
  <link rel="stylesheet" href="{{ config('app.base_url') }}/css/style.css">
</head>

<body>
  <header>
    <h1 class="text-red-600 font-bold underline">Kerangka Kerja Saya</h1>
    <nav>
      <a href="{{ config('app.base_url') }}/">Home</a> |
      <a href="{{ config('app.base_url') }}/products">Produk</a> |
      @guest
      <a href="{{ config('app.base_url') }}/login">Login</a>
      @endguest
      @auth
      <a href="{{ config('app.base_url') }}/dashboard">Dashboard</a> |
      <a href="{{ config('app.base_url') }}/logout">Logout</a>
      @endauth
    </nav>
  </header>

  <main>
    @yield('content')
  </main>

  @include('partials.footer')
</body>

</html>