<!-- templates/layouts/admin.php -->
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name') }} - @yield('title')</title>

  <link rel="stylesheet" href="{{ url('css/style.css') }}">
  <script src="{{ url('js/jquery.js') }}"></script>

  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-50">
  @include('partials.flash_alert')

  <div class="min-h-screen flex">

    @include('partials.admin_sidebar')

    <div id="main-content" class="flex-1 flex flex-col min-h-screen lg:ml-64 transition-all duration-300 ease-in-out">

      @include('partials.admin_topbar')

      <main class="flex-1 p-4 md:p-6 lg:p-8">
        <h1 class="text-base md:text-3xl font-semibold text-gray-900">
          {{ $page_title ?? 'Dashboard' }}
        </h1>
        <div class="flex gap-4 items-center mt-1 mb-6 text-base text-gray-600">
          <span class="text-primary font-medium">
            {{ $page_title ?? 'Dashboard' }}
          </span>
          @yield('breadcrumbs')
        </div>
        @yield('content')
      </main>

      <footer class="bg-white border-t border-gray-200 py-4 px-4 md:px-6 lg:px-8">
        <p class="text-sm text-gray-500 text-right">
          &copy; {{ date('Y') }} Ciboox Indonesia. All rights reserved.
        </p>
      </footer>

    </div>
  </div>

  <div id="sidebar-overlay" class="fixed inset-0 bg-gray-300/10 backdrop-blur-md z-20 lg:hidden hidden duration-100"></div>

  <script src="{{ url('/js/jquery.js') }}"></script>
  <script src="{{ url('/js/helper.js') }}"></script>
  <script>
    $(document).ready(function() {
      $('#sidebar-open-btn').on('click', function() {
        $('#sidebar').removeClass('-translate-x-full');
        $('#sidebar-overlay').removeClass('hidden');
      });

      $('#sidebar-close-btn, #sidebar-overlay').on('click', function() {
        $('#sidebar').addClass('-translate-x-full');
        $('#sidebar-overlay').addClass('hidden');
      });

      $('#profile-dropdown-btn').on('click', function() {
        $('#profile-dropdown-menu').toggleClass('hidden');
      });

      $('#sidebar').on('click', '.menu-toggle', function() {
        $(this).next('.submenu-menu').slideToggle('fast');
        $(this).find('.chevron-icon').toggleClass('rotate-180');
      });
      $(document).on('click', function(event) {
        if (!$(event.target).closest('#profile-dropdown-btn, #profile-dropdown-menu').length) {
          $('#profile-dropdown-menu').addClass('hidden');
        }
      });


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

  @stack('modals')

  @stack('scripts')
</body>

</html>