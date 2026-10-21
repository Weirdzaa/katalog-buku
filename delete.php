<?php
require 'includes/auth.php';
require 'config/database.php';
require 'includes/lang.php';
require_once 'includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($id === false) {
    set_flash('error', 'err_not_found');
    header('Location: index.php');
    exit;
}

try {
    $stmt = mysqli_prepare($conn, "DELETE FROM books WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        set_flash('success', 'msg_deleted');
    } else {
        set_flash('error', 'err_not_found');
    }
} catch (mysqli_sql_exception $e) {
    set_flash('error', 'err_delete_failed');
}

header('Location: index.php');
exit;