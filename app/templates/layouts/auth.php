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
  <main>
    @yield('content')
  </main>

  <script src="{{ url('/js/jquery.js') }}"></script>
  <script>
    $(document).ready(function() {
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