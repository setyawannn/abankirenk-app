<?php
// app/actions/feedback_management_action.php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../data/feedback_data.php';

function index_action()
{
  $db = db_connect();
  $user = auth();

  $allowed_roles = ['project_officer', 'customer_service', 'manajer_marketing'];
  if (!in_array($user['role'], $allowed_roles)) {
    flash_message('error', 'Akses Ditolak', 'Halaman ini hanya untuk PO dan CS.');
    return redirect('/dashboard');
  }

  $summary = feedback_get_summary_stats($db);
  $distribution = feedback_get_star_distribution($db);
  $trend = feedback_get_monthly_trend($db);

  $reviews_data = feedback_get_all_reviews($db, ['limit' => 10, 'page' => 1]);

  $data = [
    'page_title' => 'Manajemen Feedback',
    'active_menu' => 'feedback',
    'summary' => $summary,
    'distribution' => $distribution,
    'trend' => $trend,
    'reviews' => $reviews_data['data'],
    'pagination' => [
      'total' => $reviews_data['total'],
      'per_page' => 10,
      'current_page' => 1,
      'last_page' => ceil($reviews_data['total'] / 10)
    ]
  ];

  view('feedback_management.index', $data);
}
