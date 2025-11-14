<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>{{ url('app.name') }} - @yield('title', 'Selamat Datang')</title>

  <link rel="stylesheet" href="{{ url('css/style.css') }}">
  <script src="{{ url('js/jquery.js') }}"></script>

  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body>
  @include('partials.flash_alert')
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
  <script>
    $(document).ready(function() {
      // Flash Alert Logic
      var $alert = $('.flash-alert');

      if ($alert.length > 0) {

        function hideAlert() {
          $alert.removeClass('opacity-100 translate-x-0');
          $alert.addClass('opacity-0 translate-x-full');

          setTimeout(function() {
            $alert.closest('#alert-container').remove();
          }, 500);
        }

        setTimeout(function() {
          $alert.addClass('opacity-100 translate-x-0');
          $alert.removeClass('opacity-0 translate-x-full');
        }, 100);

        var alertTimer = setTimeout(hideAlert, 5000);

        $alert.find('.dismiss-alert').on('click', function() {
          clearTimeout(alertTimer);
          hideAlert();
        });
      }
    });
  </script>
  @stack('scripts')
</body>

</html>