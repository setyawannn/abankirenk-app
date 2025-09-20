<?php

function run_middleware($name)
{
  if ($name === null) {
    return;
  }

  switch ($name) {
    case 'auth':
      if (!isset($_SESSION['user'])) {
        redirect('/login');
      }
      break;

    case 'guest':
      if (isset($_SESSION['user'])) {
        redirect('/dashboard');
      }
      break;

    case 'admin':
      if (!isset($_SESSION['user'])) {
        redirect('/login');
      }

      if ($_SESSION['user']['role'] !== 'admin') {
        abort_403();
      }
      break;
  }
}
