<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-strings-searchbox">
	<span class="screen-reader-text"><?= __( 'Search for translatable text', 'site-reviews' ); ?></span>
	<div class="glsr-spinner"><span class="spinner"></span></div>
	<input type="search" class="glsr-strings-search" autocomplete="off" placeholder="<?= __( 'Search for translatable text...', 'site-reviews' ); ?>">
	<div class="glsr-strings-results" data-prefix="<?= $db->getOptionName(); ?>"></div>
</div>

<?= $html->renderForm( "{$tabView}/{$tabViewSection}" ); ?>

<script type="text/html" id="tmpl-glsr-string-plural">
<?php include glsr_app()->path . 'views/strings/plural.php'; ?>
</script>
<script type="text/html" id="tmpl-glsr-string-single">
<?php include glsr_app()->path . 'views/strings/single.php'; ?>
</script>
