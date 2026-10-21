<?php
require 'includes/auth.php';
require 'config/database.php';
require 'includes/lang.php';
require_once 'includes/helpers.php';

$stat = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS total,
            COALESCE(SUM(stok), 0)     AS total_stok,
            COALESCE(SUM(stok = 0), 0) AS habis
     FROM books"));

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = mysqli_prepare($conn,
        "SELECT * FROM books
         WHERE judul LIKE ? OR penulis LIKE ? OR isbn LIKE ? OR kategori LIKE ?
         ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, 'ssss', $like, $like, $like, $like);
} else {
    $stmt = mysqli_prepare($conn, "SELECT * FROM books ORDER BY id DESC");
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total  = mysqli_num_rows($result);

$page_title = t('book_list');
require 'includes/header.php';
?>

<div class="page-head">
    <div class="greeting"><?= t('welcome') ?>, <?= htmlspecialchars($_SESSION['nama']) ?> 👋</div>
    <h1><?= t('book_list') ?></h1>
</div>

<div class="stats">
    <div class="stat">
        <div class="stat-icon blue">📘</div>
        <div>
            <div class="stat-label"><?= t('total_books') ?></div>
            <div class="stat-value"><?= (int) $stat['total'] ?></div>
        </div>
    </div>
    <div class="stat">
        <div class="stat-icon green">📦</div>
        <div>
            <div class="stat-label"><?= t('col_stock') ?></div>
            <div class="stat-value"><?= (int) $stat['total_stok'] ?></div>
        </div>
    </div>
    <div class="stat">
        <div class="stat-icon red">⚠️</div>
        <div>
            <div class="stat-label"><?= t('out_of_stock') ?></div>
            <div class="stat-value"><?= (int) $stat['habis'] ?></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="toolbar">
        <form method="GET" action="index.php" class="search-form">
            <div class="search-box">
                <input type="text" name="q" value="<?= htmlspecialchars($q) ?>"
                       placeholder="<?= t('search_placeholder') ?>">
            </div>
            <button type="submit" class="btn btn-primary"><?= t('search') ?></button>
            <?php if ($q !== ''): ?>
                <a href="index.php" class="btn btn-secondary"><?= t('reset') ?></a>
            <?php endif; ?>
        </form>
        <a href="create.php" class="btn btn-primary"><?= t('add_book') ?></a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th><?= t('col_no') ?></th>
                    <th><?= t('col_isbn') ?></th>
                    <th><?= t('col_title') ?> / <?= t('col_author') ?></th>
                    <th><?= t('col_publisher') ?></th>
                    <th><?= t('col_year') ?></th>
                    <th><?= t('col_category') ?></th>
                    <th><?= t('col_stock') ?></th>
                    <th><?= t('col_action') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if ($total === 0): ?>
                <tr>
                    <td colspan="8" class="empty">
                        <?= $q !== '' ? t('no_result') : t('no_data') ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                    $stok = (int) $row['stok'];
                    if ($stok === 0) {
                        $stock_class = 'stock-out';
                    } elseif ($stok <= 5) {
                        $stock_class = 'stock-low';
                    } else {
                        $stock_class = 'stock-ok';
                    }
                    $cat_class = 'cat-' . (abs(crc32($row['kategori'])) % 6);
                    ?>
                    <tr>
                        <td class="td-no" data-label="<?= t('col_no') ?>"><?= $no++ ?></td>
                        <td data-label="<?= t('col_isbn') ?>"><span class="isbn"><?= htmlspecialchars($row['isbn']) ?></span></td>
                        <td class="td-main" data-label="<?= t('col_title') ?>">
                            <div class="book-title"><?= htmlspecialchars($row['judul']) ?></div>
                            <div class="book-author"><?= htmlspecialchars($row['penulis']) ?></div>
                        </td>
                        <td data-label="<?= t('col_publisher') ?>"><?= htmlspecialchars($row['penerbit']) ?></td>
                        <td data-label="<?= t('col_year') ?>"><?= (int) $row['tahun_terbit'] ?></td>
                        <td data-label="<?= t('col_category') ?>">
                            <span class="badge <?= $cat_class ?>"><?= cat_label($row['kategori']) ?></span>
                        </td>
                        <td data-label="<?= t('col_stock') ?>">
                            <span class="badge <?= $stock_class ?>">
                                <?= $stok === 0 ? t('out_of_stock') : $stok ?>
                            </span>
                        </td>
                        <td class="td-actions">
                            <div class="actions">
                                <a href="edit.php?id=<?= (int) $row['id'] ?>" class="btn btn-sm btn-edit"><?= t('edit') ?></a>
                                <form method="POST" action="delete.php"
                                      onsubmit="return confirm('<?= t('confirm_delete') ?>')">
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-delete"><?= t('delete') ?></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>