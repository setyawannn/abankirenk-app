<?php

/**
 * Menjalankan middleware untuk request saat ini.
 *
 * @param string|null $name Nama middleware yang akan dijalankan.
 */
function run_middleware($name)
{
  if ($name === null) {
    return;
  }

  $isLoggedIn = isset($_SESSION['user']);
  $userRole = $isLoggedIn ? $_SESSION['user']['role'] : null;

  if ($name === 'guest') {
    if ($isLoggedIn) {
      redirect('/dashboard');
    }
    return;
  }

  if (!$isLoggedIn) {
    redirect('/login');
    return;
  }

  if ($name === 'auth') {
    return;
  }

  switch ($name) {

    case 'manajer_marketing':
      if ($userRole !== 'manajer_marketing') {
        abort_403();
      }
      break;

    case 'tim_marketing':
      if ($userRole !== 'tim_marketing') {
        abort_403();
      }
      break;

    case 'project_officer':
      if ($userRole !== 'project_officer') {
        abort_403();
      }
      break;

    case 'customer_service':
      if ($userRole !== 'customer_service') {
        abort_403();
      }
      break;

    case 'desainer':
      if ($userRole !== 'desainer') {
        abort_403();
      }
      break;

    case 'tim_produksi':
      if ($userRole !== 'tim_produksi') {
        abort_403();
      }
      break;

    case 'manajer_produksi':
      if ($userRole !== 'manajer_produksi') {
        abort_403();
      }
      break;

    case 'klien':
      if ($userRole !== 'klien') {
        abort_403();
      }
      break;

    case 'layanan_purna_jual':
      if (!in_array($userRole, ['project_officer', 'customer_service'])) {
        abort_403();
      }
      break;

    default:
      abort_404();
      break;
  }
}
