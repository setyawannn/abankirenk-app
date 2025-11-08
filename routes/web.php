<?php

// routes/web.php

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

// manajer MARKETING
route_get('/manajer-marketing/manajemen-prospek', ['actions/manajer_marketing/prospek_action.php', 'index_action'], 'auth');
route_get('/manajer-marketing/manajemen-prospek/create', ['actions/manajer_marketing/prospek_action.php', 'create_action'], 'auth');
route_post('/manajer-marketing/manajemen-prospek/store', ['actions/manajer_marketing/prospek_action.php', 'store_action'], 'auth');
route_get('/manajer-marketing/manajemen-prospek/{id}/edit', ['actions/manajer_marketing/prospek_action.php', 'edit_action'], 'auth');
route_post('/manajer-marketing/manajemen-prospek/{id}/update', ['actions/manajer_marketing/prospek_action.php', 'update_action'], 'auth');
route_post('/manajer-marketing/manajemen-prospek/{id}/destroy', ['actions/manajer_marketing/prospek_action.php', 'delete_action'], 'auth');


// Tim Marketing
route_get('/tim-marketing/prospek-saya', ['actions/tim_marketing/prospek_action.php', 'index_action'], 'auth');
route_get('/tim-marketing/prospek-saya/{id}', ['actions/tim_marketing/prospek_action.php', 'edit_action'], 'auth');
route_post('/tim-marketing/prospek-saya/{id}/update', ['actions/tim_marketing/prospek_action.php', 'update_action'], 'auth');

// Project Officer
route_get('/project-officer/template-mou', ['actions/project_officer/template_mou_action.php', 'index_action'], 'project_officer');
route_get('/project-officer/template-mou/create', ['actions/project_officer/template_mou_action.php', 'create_action'], 'project_officer');
route_post('/project-officer/template-mou/store', ['actions/project_officer/template_mou_action.php', 'store_action'], 'project_officer');
route_get('/project-officer/template-mou/{id}/edit', ['actions/project_officer/template_mou_action.php', 'edit_action'], 'project_officer');
route_post('/project-officer/template-mou/{id}/update', ['actions/project_officer/template_mou_action.php', 'update_action'], 'project_officer');
route_post('/project-officer/template-mou/{id}/destroy', ['actions/project_officer/template_mou_action.php', 'delete_action'], 'project_officer');


// Desainer
route_get('/desainer/template-desain', ['actions/desainer/template_desain_action.php', 'index_action'], 'desainer');
route_get('/desainer/template-desain/create', ['actions/desainer/template_desain_action.php', 'create_action'], 'desainer');
route_post('/desainer/template-desain/store', ['actions/desainer/template_desain_action.php', 'store_action'], 'desainer');
route_get('/desainer/template-desain/{id}/edit', ['actions/desainer/template_desain_action.php', 'edit_action'], 'desainer');
route_post('/desainer/template-desain/{id}/update', ['actions/desainer/template_desain_action.php', 'update_action'], 'desainer');
route_post('/desainer/template-desain/{id}/destroy', ['actions/desainer/template_desain_action.php', 'delete_action'], 'desainer');

// AJAX Routes
route_get('/ajax/prospek', ['actions/manajer_marketing/prospek_action.php', 'ajax_list_action'], 'auth');
route_post('/ajax/prospek/update-status', ['actions/manajer_marketing/prospek_action.php', 'ajax_update_status_action'], 'auth');
route_get('/ajax/sekolah', ['actions/sekolah_action.php', 'sekolah_search_action'], 'auth');
route_post('/ajax/sekolah/store', ['actions/sekolah_action.php', 'sekolah_store_action'], 'auth');
route_post('/ajax/upload/wysiwyg', ['actions/upload_action.php', 'wysiwyg_upload_action'], null);
route_get('/ajax/template-mou', ['actions/project_officer/template_mou_action.php', 'ajax_list_action'], 'auth');
route_get('/ajax/template-desain', ['actions/desainer/template_desain_action.php', 'ajax_list_action'], 'auth');
