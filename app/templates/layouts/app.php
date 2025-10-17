<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>{{ url('app.name') }} - @yield('title', 'Selamat Datang')</title>

  <link rel="stylesheet" href="{{ url('/css/style.css') }}">
  <script src="{{ url('js/jquery.js') }}"></script>
</head>

<body>
  <header>
    <h1 class="text-red-600 font-bold underline">Kerangka Kerja Saya</h1>
    <nav>
      <a href="{{ url('/') }}">Home</a> |
      <a href="{{ url('products') }}">Produk</a> |
      @guest
      <a href="{{ url('/login') }}">Login</a>
      @endguest
      @auth
      <a href="{{ url('/dashboard') }}">Dashboard</a> |
      <a href="{{ url('/logout') }}">Logout</a>
      @endauth
    </nav>
  </header>

  <main>
    @yield('content')
  </main>

  @include('partials.footer')
  <script src="{{ url('/js/jquery.js') }}"></script>
  @stack('scripts')
</body>

</html>