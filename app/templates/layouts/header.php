<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF--T">
    <title><?= htmlspecialchars(config('app.name')); ?> - <?= htmlspecialchars($title ?? 'Selamat Datang'); ?></title>

    <link rel="stylesheet" href="<?= config('app.base_url'); ?>/css/style.css">
</head>

<body>
    <header>
        <nav>
            <a href="<?= config('app.base_url'); ?>/">Home</a> |
            <a href="<?= config('app.base_url'); ?>/about">Tentang Kami</a> |
            <a href="<?= config('app.base_url'); ?>/products">Produk</a>
        </nav>
    </header>
    <main>