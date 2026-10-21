<?php
require 'includes/auth.php';
require 'config/database.php';
require 'includes/lang.php';
require_once 'includes/helpers.php';

$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($id === false) {
    set_flash('error', 'err_not_found');
    header('Location: index.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM books WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$book = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$book) {
    set_flash('error', 'err_not_found');
    header('Location: index.php');
    exit;
}

$categories = get_categories();
$errors     = [];

$values = [
    'isbn'         => (string) $book['isbn'],
    'judul'        => (string) $book['judul'],
    'penulis'      => (string) $book['penulis'],
    'penerbit'     => (string) $book['penerbit'],
    'tahun_terbit' => (string) $book['tahun_terbit'],
    'kategori'     => (string) $book['kategori'],
    'stok'         => (string) $book['stok'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach (['judul', 'penulis', 'penerbit', 'kategori', 'tahun_terbit', 'stok'] as $f) {
        $values[$f] = trim($_POST[$f] ?? '');
    }
    $values['isbn'] = preg_replace('/[\s-]+/', '', trim($_POST['isbn'] ?? ''));

    switch (true) {
        case $values['isbn'] === '':
            $errors['isbn'] = 'err_isbn_required';
            break;
        case !ctype_digit($values['isbn']) || !in_array(strlen($values['isbn']), [10, 13], true):
            $errors['isbn'] = 'err_isbn_format';
            break;
    }


    if ($values['judul'] === '') {
        $errors['judul'] = 'err_title_required';
    } elseif (mb_strlen($values['judul']) > 150) {
        $errors['judul'] = 'err_too_long';
    }

    if ($values['penulis'] === '') {
        $errors['penulis'] = 'err_author_required';
    } elseif (mb_strlen($values['penulis']) > 100) {
        $errors['penulis'] = 'err_too_long';
    }

    if ($values['penerbit'] === '') {
        $errors['penerbit'] = 'err_publisher_required';
    } elseif (mb_strlen($values['penerbit']) > 100) {
        $errors['penerbit'] = 'err_too_long';
    }

    $tahun = filter_var($values['tahun_terbit'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1900, 'max_range' => (int) date('Y')],
    ]);
    if ($tahun === false) {
        $errors['tahun_terbit'] = 'err_year_invalid';
    }

    if (!in_array($values['kategori'], $categories, true)) {
        $errors['kategori'] = 'err_category_invalid';
    }

    $stok = filter_var($values['stok'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 0, 'max_range' => 100000],
    ]);
    if ($stok === false) {
        $errors['stok'] = 'err_stock_invalid';
    }

    if (!isset($errors['isbn'])) {
        $cek = mysqli_prepare($conn, "SELECT id FROM books WHERE isbn = ? AND id != ?");
        mysqli_stmt_bind_param($cek, 'si', $values['isbn'], $id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);
        if (mysqli_stmt_num_rows($cek) > 0) {
            $errors['isbn'] = 'err_isbn_exists';
        }
    }

    if (empty($errors)) {
        try {
            $upd = mysqli_prepare($conn,
                "UPDATE books
                 SET isbn = ?, judul = ?, penulis = ?, penerbit = ?,
                     tahun_terbit = ?, kategori = ?, stok = ?
                 WHERE id = ?");
            mysqli_stmt_bind_param($upd, 'ssssisii',
                $values['isbn'], $values['judul'], $values['penulis'], $values['penerbit'],
                $tahun, $values['kategori'], $stok, $id);
            mysqli_stmt_execute($upd);

            set_flash('success', 'msg_updated');
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            $errors['_db'] = 'err_db';
        }
    }
}

$form_action = 'edit.php?id=' . $id;
$page_title  = t('form_edit_title');
require 'includes/header.php';
?>

<div class="card">
    <h2><?= t('form_edit_title') ?></h2>
    <div class="subtitle"><?= htmlspecialchars($book['judul']) ?></div>

    <?php require 'includes/book_form.php'; ?>
</div>

<?php require 'includes/footer.php'; ?>