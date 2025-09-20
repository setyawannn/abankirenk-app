<?php

// Daftarkan semua rute aplikasi di sini.
// Format: route_get('url', ['path/ke/controller.php', 'nama_fungsi']);

route_get('/', ['controllers/home_controller.php', 'index_action']);
route_get('/about', ['controllers/home_controller.php', 'about_action']);
route_get('/products', ['controllers/product_controller.php', 'products_list_action']);

route_get('/login', ['controllers/auth_controller.php', 'login_form_action'], 'guest');
route_post('/login', ['controllers/auth_controller.php', 'login_process_action'], 'guest');
route_get('/register', ['controllers/auth_controller.php', 'register_form_action'], 'guest');
route_post('/register', ['controllers/auth_controller.php', 'register_process_action'], 'guest');

route_get('/dashboard', ['controllers/dashboard_controller.php', 'index_action'], 'auth');
route_get('/logout', ['controllers/auth_controller.php', 'logout_action'], 'auth');

route_get('/admin/settings', ['controllers/admin_controller.php', 'settings_action'], 'admin');
