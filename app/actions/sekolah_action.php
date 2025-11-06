<?php

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../data/sekolah_data.php';


function sekolah_search_action() {
    $db = db_connect();
    header('Content-Type: application/json');
    $sekolah = sekolah_get_all($db);
    echo json_encode($sekolah);
}

function sekolah_store_action() {
    $db = db_connect();
    header('Content-Type: application/json');

    if (empty($_POST['nama']) || empty($_POST['lokasi']) || empty($_POST['kontak'])) {
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
        return;
    }

    $id_sekolah = sekolah_insert($db, $_POST['nama'], $_POST['lokasi'], $_POST['kontak']);

    if ($id_sekolah) {
        $sekolah = sekolah_get_by_id($db, $id_sekolah);
        echo json_encode(['success' => true, 'sekolah' => $sekolah]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan sekolah baru']);
    }
}