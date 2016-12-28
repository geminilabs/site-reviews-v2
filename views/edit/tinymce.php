<?php defined( 'WPINC' ) || die; ?>

<div class="sc-wrap">
	<button class="button sc-button">
		<span class="wp-media-buttons-icon"></span>
	</button>
	<div class="sc-menu mce-menu">
	<?php foreach( $shortcodes as $key => $values ) : ?>
		<div class="sc-shortcode mce-menu-item glsr-shortcode-item-<?= $key; ?>" data-shortcode="<?= $key; ?>"><?= $values['label']; ?></div>
	<?php endforeach; ?>
	</div>
</div>
