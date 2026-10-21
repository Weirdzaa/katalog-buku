<?php
session_start();

$allowed = ['id', 'en'];
$lang    = $_GET['lang'] ?? '';

if (in_array($lang, $allowed, true)) {
    $_SESSION['lang'] = $lang;
}

$back = $_GET['back'] ?? 'index.php';
if (!preg_match('/^[a-z_]+\.php(\?[A-Za-z0-9=&_%+.\-]*)?$/', $back)) {
    $back = 'index.php';
}

header('Location: ' . $back);
exit;