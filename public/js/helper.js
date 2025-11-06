function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
};

// MODAL HELPERS
function showModal(modalId) {
  const $modal = $(modalId);
  if (!$modal.length) return;

  const $overlay = $modal.find('.modal-overlay');
  const $modalBox = $modal.find('.modal-box');

  $modal.removeClass('invisible');

  setTimeout(() => {
    $overlay.removeClass('opacity-0');
    $modalBox.removeClass('opacity-0 scale-95');
  }, 10);
}

function hideModal(modalId) {
  const $modal = $(modalId);
  if (!$modal.length) return;

  const $overlay = $modal.find('.modal-overlay');
  const $modalBox = $modal.find('.modal-box');

  $overlay.addClass('opacity-0');
  $modalBox.addClass('opacity-0 scale-95');

  setTimeout(() => {
    $modal.addClass('invisible');
  }, 300);
}

$(document).ready(function () {

  $(document).on('click', '[data-modal-target]', function () {
    var modalId = $(this).data('modal-target');
    showModal(modalId);
  });

  $(document).on('click', '[data-modal-dismiss]', function () {
    var modalId = $(this).data('modal-dismiss');
    hideModal(modalId);
  });

  $(document).on('click', '.modal-container', function (e) {
    if ($(e.target).hasClass('modal-overlay')) {
      hideModal(this);
    }
  });

});

// TOAST HELPERS
function showGlobalToast(key, title, message) {
  $('#alert-container').remove();

  let iconName = '';
  let colorClasses = '';

  switch (key) {
    case 'success':
      iconName = 'checkmark-circle-outline';
      colorClasses = 'text-green-600';
      break;
    case 'error':
      iconName = 'close-circle-outline';
      colorClasses = 'text-red-600';
      break;
    case 'warning':
      iconName = 'warning-outline';
      colorClasses = 'text-yellow-500';
      break;
    case 'info':
    default:
      iconName = 'information-circle-outline';
      colorClasses = 'text-blue-500';
      break;
  }

  const alertHtml = `
    <div id="alert-container" class="fixed top-4 right-4 z-50 w-full max-w-sm space-y-3 pointer-events-none">
        <div
            role="alert"
            class="flash-alert dynamic-alert rounded-md border border-gray-300 bg-white p-4 shadow-lg 
                   transform transition-all duration-500 ease-in-out 
                   translate-x-full opacity-0 pointer-events-auto">
            <div class="flex items-start gap-4">

                <div class="${colorClasses}">
                    <ion-icon name="${iconName}" class="w-6 h-6"></ion-icon>
                </div>

                <div class="flex-1">
                    ${title ? `<strong class="font-medium ${colorClasses}">${escapeHTML(title)}</strong>` : ''}
                    <p class="mt-0.5 text-sm text-gray-700">${escapeHTML(message)}</p>
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
    `;

  $('body').append(alertHtml);

  const $alertContainer = $('#alert-container');
  const $alert = $alertContainer.find('.dynamic-alert');

  function hideAlert() {
    $alert.removeClass('opacity-100 translate-x-0');
    $alert.addClass('opacity-0 translate-x-full');
    setTimeout(() => {
      $alertContainer.remove();
    }, 500);
  }

  setTimeout(() => {
    $alert.addClass('opacity-100 translate-x-0');
    $alert.removeClass('opacity-0 translate-x-full');
  }, 100);

  const alertTimer = setTimeout(hideAlert, 5000);

  $alert.on('click', '.dismiss-alert', function () {
    clearTimeout(alertTimer);
    hideAlert();
  });
}

function escapeHTML(str) {
  if (typeof str !== 'string') return '';
  return str.replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m]);
}


