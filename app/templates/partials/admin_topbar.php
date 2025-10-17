@php
$user = auth();
@endphp
<header class="sticky top-0 z-10 bg-white border-b border-gray-200 flex-shrink-0">
  <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center space-x-4">
      <button id="sidebar-open-btn" class="lg:hidden text-gray-500 hover:text-gray-700 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <ion-icon name="menu-outline" class="w-6 h-6"></ion-icon>
      </button>
      <h1 class="text-base md:text-lg font-semibold text-gray-900">
        {{ $page_title ?? 'Dashboard' }}
      </h1>
    </div>

    <div class="flex items-center">
      <div class="relative">
        <button id="profile-dropdown-btn" class="flex items-center space-x-2 rounded-full p-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
          <img class="h-9 w-9 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user['name']) }}&background=4d94df&color=fff" alt="User avatar">
          <div class="flex-col items-start leading-tight hidden  md:flex">
            <p class="text-sm font-medium text-gray-900">{{ $user['name'] }}</p>
            <p class="text-xs text-gray-500 truncate">{{ $user['email'] ?? '404@emailnotfound' }}</p>
          </div>
          <ion-icon name="chevron-down-outline" class="h-5 w-5 text-gray-500 hidden sm:block"></ion-icon>
        </button>
        <div id="profile-dropdown-menu" class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg border origin-top-right hidden">
          <div class="px-4 py-3 border-b">
            <p class="text-sm font-medium text-gray-900">{{ $user['name'] }}</p>
            <p class="text-xs text-gray-500 truncate">{{ $user['email'] ?? 'email@notfound.com' }}</p>
          </div>
          <div class="py-1">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Anda</a>
            <a href="{{ url('/logout') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Keluar
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>