<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>{{ url('app.name') }} - @yield('title', 'Selamat Datang')</title>

  <link rel="stylesheet" href="{{ url('css/style.css') }}?{{ time() }}">
  <script src="{{ url('js/jquery.js') }}"></script>

  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body>
  @include('partials.flash_alert')

  <header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-red-600 tracking-tighter">AbankIrenk</a>
          </div>
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <a href="{{ url('/') }}"
              class="text-gray-900 hover:text-red-600 inline-flex items-center px-1 pt-1 text-sm font-medium transition">Home</a>
            <a href="{{ url('products') }}"
              class="text-gray-500 hover:text-red-600 inline-flex items-center px-1 pt-1 text-sm font-medium transition">Produk</a>
          </div>
        </div>
        <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
          @guest
          <a href="{{ url('/login') }}"
            class="text-gray-500 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium transition">Login</a>
          <a href="{{ url('/register') }}"
            class="bg-red-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-red-700 transition shadow-md">Daftar</a>
          @endguest
          @auth
          <a href="{{ url('/dashboard') }}"
            class="text-gray-500 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium transition">Dashboard</a>
          <a href="{{ url('/logout') }}"
            class="text-gray-500 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium transition">Logout</a>
          @endauth
        </div>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>

  @include('partials.footer')
  <script src="{{ url('/js/jquery.js') }}"></script>
  <script>
    $(document).ready(function () {
      // Flash Alert Logic
      var $alert = $('.flash-alert');

      if ($alert.length > 0) {

        function hideAlert() {
          $alert.removeClass('opacity-100 translate-x-0');
          $alert.addClass('opacity-0 translate-x-full');

          setTimeout(function () {
            $alert.closest('#alert-container').remove();
          }, 500);
        }

        setTimeout(function () {
          $alert.addClass('opacity-100 translate-x-0');
          $alert.removeClass('opacity-0 translate-x-full');
        }, 100);

        var alertTimer = setTimeout(hideAlert, 5000);

        $alert.find('.dismiss-alert').on('click', function () {
          clearTimeout(alertTimer);
          hideAlert();
        });
      }
    });
  </script>
  @stack('scripts')
</body>

</html>