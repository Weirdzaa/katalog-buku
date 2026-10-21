<?php
require_once __DIR__ . '/helpers.php';

$css_version = @filemtime(__DIR__ . '/../assets/css/style.css') ?: 1;

$initial = mb_strtoupper(mb_substr($_SESSION['nama'] ?? 'U', 0, 1, 'UTF-8'), 'UTF-8');
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page_title ?> - <?= t('app_title') ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= $css_version ?>">
</head>
<body>
<header class="navbar">
    <div class="navbar-inner">
        <a href="index.php" class="brand">
            <span class="logo">📚</span>
            <span><?= t('app_title') ?></span>
        </a>
        <div class="nav-right">
            <?php include __DIR__ . '/lang_switcher.php'; ?>
            <div class="user">
                <span class="avatar"><?= htmlspecialchars($initial) ?></span>
                <span class="user-name"><?= htmlspecialchars($_SESSION['nama']) ?></span>
            </div>
            <a href="logout.php" class="btn btn-ghost btn-sm"><?= t('logout') ?></a>
        </div>
    </div>
</header>
<main class="container">
<?php show_flash(); ?>