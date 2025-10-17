@php
$user = auth();
$userRole = $user ? $user['role'] : 'klien'; 

$menuConfig = config('menu', []);
$menuItems = $menuConfig[$userRole] ?? [];

$active_menu = $active_menu ?? ''; 
$active_parent_id = null;

if ($active_menu) {
    foreach ($menuItems as $item) {
        if (isset($item['children']) && count($item['children']) > 0) {
            foreach ($item['children'] as $child) {
                if ($child['id'] === $active_menu) {
                    $active_parent_id = $item['id'];
                    break 2; 
                }
            }
        }
    }
}
@endphp

<aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col">
  <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 flex-shrink-0">
    <a href="{{ url('/dashboard') }}" class="flex items-center space-x-3">
      <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
        <span class="text-white font-bold text-lg">C</span>
      </div>
      <span class="text-lg font-semibold text-gray-900">Conseling</span>
    </a>
    <button id="sidebar-close-btn" class="lg:hidden text-gray-500 hover:text-gray-700">
      <ion-icon name="close-outline" class="w-6 h-6"></ion-icon>
    </button>
  </div>

  <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-2">
    
    @foreach ($menuItems as $item)
      @php
        $hasChildren = isset($item['children']) && count($item['children']) > 0;
        
        $isParentActive = ($active_menu === $item['id'] || $active_parent_id === $item['id']);
      @endphp

      @if (!$hasChildren)
        <a href="{{ url($item['redirect_url']) }}" 
           class="flex items-center space-x-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 
                  {{ $active_menu === $item['id'] ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
          <ion-icon name="{{ $item['icon'] }}" class="w-5 h-5"></ion-icon>
          <span>{{ $item['nama'] }}</span>
        </a>
      @else
        <div>
          <button 
             class="menu-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 
                    {{ $isParentActive ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
            <div class="flex items-center space-x-3">
              <ion-icon name="{{ $item['icon'] }}" class="w-5 h-5"></ion-icon>
              <span>{{ $item['nama'] }}</span>
            </div>
            <ion-icon name="chevron-down-outline" 
                      class="chevron-icon w-4 h-4 transition-transform duration-200 {{ $isParentActive ? 'rotate-180' : '' }}"></ion-icon>
          </button>
          
          <div class="submenu-menu ml-4 mt-2 space-y-1 {{ $isParentActive ? '' : 'hidden' }}">
            @foreach ($item['children'] as $child)
              <a href="{{ url($child['redirect_url']) }}" 
                 class="block px-4 py-2 text-sm rounded-md 
                        {{ $active_menu === $child['id'] ? 'text-primary font-medium' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                {{ $child['nama'] }}
              </a>
            @endforeach
          </div>
        </div>
      @endif
    @endforeach
  </nav>

  {{-- <div class="border-t border-gray-200 p-4 flex-shrink-0">
    <div class="flex items-center space-x-3">
      <img src="https://ui-avatars.com/api/?name={{ urlencode($user['name'] ?? 'User') }}&background=F97B06&color=fff" alt="User Avatar" class="w-10 h-10 rounded-full">
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-900 truncate">{{ $user['name'] ?? 'Nama Pengguna' }}</p>
        <p class="text-xs text-gray-500 truncate">{{ $user['email'] ?? 'email@notfound.com' }}</p> 
      </div>
    </div>
  </div> --}}
</aside>