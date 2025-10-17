<?php
if (isset($_SESSION['flash_message'])) {
    $flash = $_SESSION['flash_message'];
    $key = $flash['key'];
    $title = $flash['title'];
    $message = $flash['message'];

    

    $icon = '';
    $colorClasses = '';

    switch ($key) {
        case 'success':
            $icon = 'checkmark-circle-outline';
            $colorClasses = 'text-green-600'; 
            break;
        case 'error':
            $icon = 'close-circle-outline';
            $colorClasses = 'text-red-600';
            break;
        case 'warning':
            $icon = 'warning-outline';
            $colorClasses = 'text-yellow-500';
            break;
        case 'info':
        default:
            $icon = 'information-circle-outline';
            $colorClasses = 'text-blue-500';
            break;
    }
    unset($_SESSION['flash_message']);
}
@endphp

@if (isset($flash))
<div id="alert-container" class="fixed top-4 right-4 z-50 w-full max-w-sm space-y-3 pointer-events-none">

  <div
    role="alert"
    class="flash-alert rounded-md border border-gray-300 bg-white p-4 shadow-lg 
             transform transition-all duration-500 ease-in-out 
             translate-x-full opacity-0 pointer-events-auto">
    <div class="flex items-start gap-4">

      <div class="{{ $colorClasses }}">
        <ion-icon name="{{ $icon }}" class="w-6 h-6"></ion-icon>
      </div>

      <div class="flex-1">
        <strong class="font-medium {{ $colorClasses }}">{{ $title }}</strong>
        <p class="mt-0.5 text-sm text-gray-700">{{ $message }}</p>
      </div>

      <button
        class="dismiss-alert -m-3 rounded-full p-1.5 text-gray-500 transition-colors hover:bg-gray-50 hover:text-gray-700"
        type="button"
        aria-label="Dismiss alert">
        <span class="sr-only">Dismiss popup</span>
        <ion-icon name="close-outline" class="w-5 h-5"></ion-icon>
      </button>
    </div>
  </div>
</div>
@endif