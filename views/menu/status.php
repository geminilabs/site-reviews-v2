<?php defined( 'WPINC' ) || die; ?>

<form method="post" action="">

	<?php
		echo $html->p( sprintf(
			__( "All dates and times shown here use the WordPress <a href='%s'>configured timezone</a>.", 'site-reviews' ),
			admin_url( 'options-general.php' )
		));
	?>

	<table class="wp-list-table widefat fixed striped glsr-status">

		<thead>
			<tr>
				<th class="site"><?= __( 'Site', 'site-reviews' ); ?></th>
				<th class="total-fetched column-primary"><?= __( 'Reviews', 'site-reviews' ); ?></th>
				<th class="last-fetch"><?= __( 'Last fetch', 'site-reviews' ); ?></th>
				<th class="next-fetch"><?= __( 'Next scheduled fetch', 'site-reviews' ); ?></th>
			</tr>
		</thead>

		<tbody>

		<?php foreach( $tabs['settings']['sections'] as $key => $title ) : ?>

			<tr data-site="<?= $key; ?>">
				<td class="site">
					<a href="<?= admin_url( 'edit.php?post_type=site-review&page=' . glsr_app()->id . "&tab=settings&section={$key}" ); ?>"><?= $title; ?></a>
				</td>
				<td class="total-fetched column-primary">
					<a href="<?= admin_url( "edit.php?post_type=site-review&post_status=all&site_name={$key}" ); ?>"><?= $db->getReviewCount( 'site_name', $key ); ?></a>
					<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
				</td>
				<td class="last-fetch" data-colname="<?= __( 'Last fetch', 'site-reviews' ); ?>">
					<?= $db->getOption( $key, __( 'No fetch has been completed', 'site-reviews' ), 'last_fetch' ); ?>
				</td>
				<td class="next-fetch" data-colname="<?= __( 'Next scheduled fetch', 'site-reviews' ); ?>">
					<?= $db->getOption( $key, __( 'Nothing currently scheduled', 'site-reviews' ), 'next_fetch' ); ?>
				</td>
			</tr>

		<?php endforeach; ?>

		</tbody>

	</table>

	<br>

	<hr>

	<table class="form-table">
		<tbody>

		<?php

			echo $html->row()->select( 'site', [
				'label'      => __( 'Fetch reviews from', 'site-reviews' ),
				'options'    => $tabs['settings']['sections'],
				'attributes' => 'data-site',
				'prefix'     => glsr_app()->prefix,
			]);

			echo $html->row()->progress([
				'label'  => __( 'Fetch status', 'site-reviews' ),
				'active' => __( 'Fetching reviews...', 'site-reviews' ),
				'class'  => 'green',
			]);
		?>

		</tbody>
	</table>

	<?php wp_nonce_field( 'fetch-reviews' ); ?>

	<?php printf( '<input type="hidden" name="%s[action]" value="fetch-reviews">', glsr_app()->prefix ); ?>

	<?php submit_button( __( 'Fetch Reviews', 'site-reviews' ), 'large primary', 'fetch-reviews' ); ?>

</form>
