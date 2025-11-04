<?php

// Daftarkan semua rute aplikasi di sini.
// Format: route_get('url', ['path/ke/controller.php', 'nama_fungsi']);

route_get('/', ['actions/home_action.php', 'index_action']);
route_get('/about', ['actions/home_action.php', 'about_action']);
route_get('/products', ['actions/product_action.php', 'products_list_action']);

route_get('/login', ['actions/auth_action.php', 'login_form_action'], 'guest');
route_post('/login', ['actions/auth_action.php', 'login_process_action'], 'guest');
route_get('/register', ['actions/auth_action.php', 'register_form_action'], 'guest');
route_post('/register', ['actions/auth_action.php', 'register_process_action'], 'guest');

route_get('/dashboard', ['actions/dashboard_action.php', 'index_action'], 'auth');
route_get('/logout', ['actions/auth_action.php', 'logout_action'], 'auth');

route_get('/admin/settings', ['actions/admin_action.php', 'settings_action'], 'admin');

// MANAGER MARKETING
route_get('/manager-marketing/manajemen-prospek', ['actions/manajer_marketing/prospek_action.php', 'index_action'], 'auth');
route_get('/manager-marketing/manajemen-prospek/create', ['actions/manajer_marketing/prospek_action.php', 'create_action'], 'auth');
route_post('/manager-marketing/manajemen-prospek', ['actions/manajer_marketing/prospek_action.php', 'store_action'], 'auth');
route_get('/manager-marketing/manajemen-prospek/{id}/edit', ['actions/manajer_marketing/prospek_action.php', 'edit_action'], 'auth');
route_post('/manager-marketing/manajemen-prospek/{id}', ['actions/manajer_marketing/prospek_action.php', 'update_action'], 'auth');

// AJAX Routes
route_get('/ajax/prospek', ['actions/manajer_marketing/prospek_action.php', 'ajax_list_action'], 'auth');
