<?php
require 'includes/auth.php';
require 'config/database.php';
require 'includes/lang.php';
require_once 'includes/helpers.php';

$categories = get_categories();
$errors     = [];
$values     = [
    'isbn' => '', 'judul' => '', 'penulis' => '', 'penerbit' => '',
    'tahun_terbit' => '', 'kategori' => '', 'stok' => '0',
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
        $cek = mysqli_prepare($conn, "SELECT id FROM books WHERE isbn = ?");
        mysqli_stmt_bind_param($cek, 's', $values['isbn']);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);
        if (mysqli_stmt_num_rows($cek) > 0) {
            $errors['isbn'] = 'err_isbn_exists';
        }
    }

    if (empty($errors)) {
        try {
            $stmt = mysqli_prepare($conn,
                "INSERT INTO books (isbn, judul, penulis, penerbit, tahun_terbit, kategori, stok)
                 VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssssisi',
                $values['isbn'], $values['judul'], $values['penulis'], $values['penerbit'],
                $tahun, $values['kategori'], $stok);
            mysqli_stmt_execute($stmt);

            set_flash('success', 'msg_created');
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            $errors['_db'] = 'err_db';
        }
    }
}

$form_action = 'create.php';
$page_title  = t('form_add_title');
require 'includes/header.php';
?>

<div class="card">
    <h2><?= t('form_add_title') ?></h2>
    <div class="subtitle"><?= t('app_title') ?></div>

    <?php require 'includes/book_form.php'; ?>
</div>

<?php require 'includes/footer.php'; ?>