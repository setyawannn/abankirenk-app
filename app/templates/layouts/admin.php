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

  <div class="min-h-screen flex">

    @include('partials.admin_sidebar')

    <div id="main-content" class="flex-1 flex flex-col min-h-screen lg:ml-64 transition-all duration-300 ease-in-out">

      @include('partials.admin_topbar')

      <main class="flex-1 p-4 md:p-6 lg:p-8">
        @yield('content')
      </main>

      <footer class="bg-white border-t border-gray-200 py-4 px-4 md:px-6 lg:px-8">
        <p class="text-sm text-gray-500 text-right">
          &copy; {{ date('Y') }} Ciboox Indonesia. All rights reserved.
        </p>
      </footer>

    </div>
  </div>

  <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-20 lg:hidden hidden"></div>

  <script src="{{ url('/js/jquery.js') }}"></script>
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

      $('#settings-toggle').on('click', function() {
        $('#settings-menu').slideToggle('fast');
        $(this).find('ion-icon[name="chevron-down-outline"]').toggleClass('rotate-180');
      });

      $(document).on('click', function(event) {
        if (!$(event.target).closest('#profile-dropdown-btn, #profile-dropdown-menu').length) {
          $('#profile-dropdown-menu').addClass('hidden');
        }
      });
    });
  </script>
  @stack('scripts')
</body>

</html>