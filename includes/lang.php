<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$available_langs = ['id', 'en'];

if (!isset($_SESSION['lang']) || !in_array($_SESSION['lang'], $available_langs, true)) {
    $_SESSION['lang'] = 'id';
}

$current_lang = $_SESSION['lang'];
$lang = require __DIR__ . '/../lang/' . $current_lang . '.php';

function t($key) {
    global $lang;
    return htmlspecialchars($lang[$key] ?? $key, ENT_QUOTES, 'UTF-8');
}