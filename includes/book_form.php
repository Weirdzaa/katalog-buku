<?php
$fields = [
    'isbn'         => 'col_isbn',
    'judul'        => 'col_title',
    'penulis'      => 'col_author',
    'penerbit'     => 'col_publisher',
    'tahun_terbit' => 'col_year',
    'kategori'     => 'col_category',
    'stok'         => 'col_stock',
];
?>
<form method="POST" action="<?= htmlspecialchars($form_action) ?>" novalidate>
    <div class="required-note"><?= t('form_required_note') ?></div>

    <?php if (isset($errors['_db'])): ?>
        <div class="flash flash-error"><?= t($errors['_db']) ?></div>
    <?php endif; ?>

    <div class="form-grid">
        <?php foreach ($fields as $name => $label_key): ?>
            <div class="form-group <?= $name === 'judul' ? 'span-2' : '' ?>">
                <label for="<?= $name ?>"><?= t($label_key) ?> *</label>

                <?php if ($name === 'kategori'): ?>
                    <select id="kategori" name="kategori" class="<?= isset($errors['kategori']) ? 'has-error' : '' ?>">
                        <option value=""><?= t('select_category') ?></option>
                        <?php foreach ($categories as $cat): ?>
                            <?php?>
                            <option value="<?= htmlspecialchars($cat) ?>"
                                <?= $values['kategori'] === $cat ? 'selected' : '' ?>>
                                <?= cat_label($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="text" id="<?= $name ?>" name="<?= $name ?>"
                           value="<?= htmlspecialchars($values[$name]) ?>"
                           class="<?= isset($errors[$name]) ? 'has-error' : '' ?>"
                           <?= in_array($name, ['tahun_terbit', 'stok'], true) ? 'inputmode="numeric"' : '' ?>>
                <?php endif; ?>

                <?php field_error($errors, $name); ?>
                <?php if ($name === 'isbn'): ?><small><?= t('hint_isbn') ?></small><?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= t('save') ?></button>
        <a href="index.php" class="btn btn-secondary"><?= t('cancel') ?></a>
    </div>
</form>