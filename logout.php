<?php
session_start();
$lang = $_SESSION['lang'] ?? 'id';

$_SESSION = [];
session_destroy();

session_start();
$_SESSION['lang'] = $lang;

header('Location: login.php');
exit;