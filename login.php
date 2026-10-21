<?php
session_start();
require 'config/database.php';
require 'includes/lang.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = t('err_required');
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, password, nama_lengkap FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama_lengkap'];
            header('Location: index.php');
            exit;
        } else {
            $error = t('err_invalid');
        }
    }
}

$css_version = @filemtime(__DIR__ . '/assets/css/style.css') ?: 1;
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= t('login') ?> - <?= t('app_title') ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= $css_version ?>">
</head>
<body class="auth">
    <div class="auth-lang">
        <?php include 'includes/lang_switcher.php'; ?>
    </div>

    <main class="auth-card">
        <div class="auth-logo">📚</div>
        <h1><?= t('app_title') ?></h1>

        <?php if ($error !== ''): ?>
            <div class="flash flash-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username"><?= t('username') ?></label>
                <input type="text" id="username" name="username" autocomplete="username" autofocus
                       value="<?= htmlspecialchars($username) ?>">
            </div>

            <div class="form-group">
                <label for="password"><?= t('password') ?></label>
                <input type="password" id="password" name="password" autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary btn-block"><?= t('login') ?></button>
        </form>
    </main>
</body>
</html>