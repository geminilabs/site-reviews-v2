<?php defined( 'WPINC' ) || die; ?>

<p><?= __( 'Enter a Page/Post ID here to assign this review to it.', 'site-reviews' ); ?></p>
<p>
	<span class="spinner"></span>
	<input type="number" name="assigned_to" id="assigned_to" class="small-text" value="<?= $ID; ?>">
	<span class="description"><?= $permalink; ?></span>
</p>
