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


function showModal(modalId) {
  const $modal = $(modalId);
  if (!$modal.length) return;

  const $overlay = $modal.find('.modal-overlay');
  const $modalBox = $modal.find('.modal-box');

  // 1. Tampilkan modal container (menghapus 'invisible')
  //    'flex' sudah harus ada di HTML-nya.
  $modal.removeClass('invisible');

  // 2. Tunggu "paint", lalu jalankan animasi
  setTimeout(() => {
    $overlay.removeClass('opacity-0'); // Fade-in overlay
    $modalBox.removeClass('opacity-0 scale-95'); // Fade-in & scale-up box
  }, 10);
}

/**
 * Menyembunyikan modal secara programatik dengan animasi.
 * @param {string} modalId - ID dari modal (e.g., "#myModal")
 */
function hideModal(modalId) {
  const $modal = $(modalId);
  if (!$modal.length) return;

  const $overlay = $modal.find('.modal-overlay');
  const $modalBox = $modal.find('.modal-box');

  // 1. Jalankan animasi "keluar"
  $overlay.addClass('opacity-0'); // Fade-out overlay
  $modalBox.addClass('opacity-0 scale-95'); // Fade-out & scale-down box

  // 2. Setelah animasi selesai (300ms), sembunyikan modal
  setTimeout(() => {
    $modal.addClass('invisible');
  }, 300); // Durasi ini HARUS SAMA dengan durasi transisi
}


// ====================================================================
// LOGIKA "BOOTSTRAP-LIKE" (data-modal-target)
// (Bagian ini tidak berubah)
// ====================================================================
$(document).ready(function () {

  $(document).on('click', '[data-modal-target]', function () {
    var modalId = $(this).data('modal-target');
    showModal(modalId);
  });

  $(document).on('click', '[data-modal-dismiss]', function () {
    var modalId = $(this).data('modal-dismiss');
    hideModal(modalId);
  });

  // 3. Klik Overlay untuk Menutup
  $(document).on('click', '.modal-container', function (e) {
    // Hanya jika yang diklik adalah overlay-nya
    if ($(e.target).hasClass('modal-overlay')) {
      hideModal(this);
    }
  });

});

