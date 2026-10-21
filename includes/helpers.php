<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function get_categories() {
    return ['Fiksi', 'Sastra', 'Pengembangan Diri', 'Teknologi', 'Sejarah', 'Bisnis', 'Sains', 'Lainnya'];
}

function cat_label($cat) {
    global $lang;
    $key = 'cat_' . strtolower(str_replace(' ', '_', $cat));
    if (isset($lang[$key])) {
        return htmlspecialchars($lang[$key], ENT_QUOTES, 'UTF-8');
    }
    return htmlspecialchars($cat, ENT_QUOTES, 'UTF-8');
}

function set_flash($type, $key) {
    $_SESSION['flash'] = ['type' => $type, 'key' => $key];
}

function show_flash() {
    if (!isset($_SESSION['flash'])) {
        return;
    }
    $type = $_SESSION['flash']['type'] === 'error' ? 'error' : 'success';
    echo '<div class="flash flash-' . $type . '">' . t($_SESSION['flash']['key']) . '</div>';
    unset($_SESSION['flash']);
}

function field_error($errors, $name) {
    if (isset($errors[$name])) {
        echo '<div class="field-error">' . t($errors[$name]) . '</div>';
    }
}