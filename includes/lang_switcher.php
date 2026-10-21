<?php
$back = basename($_SERVER['SCRIPT_NAME']);
if (!empty($_SERVER['QUERY_STRING'])) {
    $back .= '?' . $_SERVER['QUERY_STRING'];
}
?>
<div class="lang-switch" role="group" aria-label="Language">
    <a href="set_lang.php?lang=id&back=<?= urlencode($back) ?>"
       class="<?= $current_lang === 'id' ? 'active' : '' ?>">ID</a>
    <a href="set_lang.php?lang=en&back=<?= urlencode($back) ?>"
       class="<?= $current_lang === 'en' ? 'active' : '' ?>">EN</a>
</div>