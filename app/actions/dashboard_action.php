<?php

function index_action()
{

  $data = [
    'title' => 'Dashboard',
    'active_menu' => 'dash_klien',
  ];

  view('dashboard.index', $data);
}
