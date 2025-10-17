<?php

function index_action()
{

  $data = [
    'title' => 'Dashboard',
    'active_menu' => 'feedback_po',
  ];

  view('dashboard.index', $data);
}
