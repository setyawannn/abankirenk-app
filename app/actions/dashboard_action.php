<?php

function index_action()
{
  $currentUser = auth();


  $adminData = [
    'total_users' => 150,
    'server_status' => 'Online'
  ];

  $userData = [
    'unread_messages' => 5,
    'last_login' => '2025-09-21 10:30:00'
  ];

  $data = [
    'title' => 'Dashboard',
    'user' => $currentUser,
    'admin_stats' => ($currentUser['role'] === 'admin') ? $adminData : [],
    'user_stats' => ($currentUser['role'] === 'user') ? $userData : []
  ];

  view('dashboard.index', $data);
}
