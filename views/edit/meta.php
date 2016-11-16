<?php defined( 'WPINC' ) or die; ?>

<?php
	$siteName = get_post_meta( $post->ID, 'site_name', true );

	if( $siteName == 'local' ) {
		$siteName = __( 'Local Review', 'site-reviews' );
	}

	$reviewUrl = get_post_meta( $post->ID, 'url', true );

	if( $reviewUrl ) {
		$siteName = sprintf( '<a href="%s" target="_blank">%s</a>', $reviewUrl, ucfirst( $siteName ) );
	}

	$modified = false;

	if( $post->post_title !== get_post_meta( $post->ID, 'title', true )
		|| $post->post_content !== get_post_meta( $post->ID, 'content', true )
		|| $post->post_date !== get_post_meta( $post->ID, 'date', true ) ) {
		$modified = true;
	}

	$revertUrl = wp_nonce_url( admin_url( sprintf( 'post.php?post=%s&action=revert', $post->ID ) ), 'revert-review_' . $post->ID );

	$revert = !$modified
		? __( 'Nothing to Revert', 'site-reviews' )
		: __( 'Revert Changes', 'site-reviews' );

	$revertButton = !$modified
		? sprintf( '<button id="revert" class="button button-large" disabled>%s</button>', $revert )
		: sprintf( '<a href="%s" id="revert" class="button button-large">%s</a>', $revertUrl, $revert );
?>

<table class="glsr-metabox-table">
	<tbody>
		<tr>
			<td><?= __( 'Rating', 'site-reviews' ); ?></th>
			<td><?php $html->renderPartial( 'rating', ['stars' => get_post_meta( $post->ID, 'rating', true ) ] ); ?></td>
		</tr>
		<tr>
			<td><?= __( 'Type', 'site-reviews' ); ?></th>
			<td><?= $siteName; ?></td>
		</tr>
		<tr>
			<td><?= __( 'Date', 'site-reviews' ); ?></th>
			<td><?= get_date_from_gmt( get_post_meta( $post->ID, 'date', true ), 'F j, Y' ); ?></td>
		</tr>
		<tr>
			<td><?= __( 'Reviewer', 'site-reviews' ); ?></th>
			<td><?= get_post_meta( $post->ID, 'author', true ); ?></td>
		</tr>
		<tr>
			<td><?= __( 'Avatar', 'site-reviews' ); ?></th>
			<td><img src="<?= get_post_meta( $post->ID, 'avatar', true ); ?>" width="96"></td>
		</tr>
	</tbody>
</table>

<div class="revert-action">
	<span class="spinner"></span>
	<?= $revertButton; ?>
</div>
