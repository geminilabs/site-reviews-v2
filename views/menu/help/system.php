<?php defined( 'WPINC' ) or die; ?>

<br>

<form method="post"><?php
	wp_nonce_field( 'download-system-info' );
	printf( '<textarea rows="13" name="%s[system-info]" class="glsr-log" onclick="this.select()" readonly>%s</textarea>', glsr_app()->prefix, $system_info->getAll() );
	printf( '<input type="hidden" name="%s[action]" value="download-system-info">', glsr_app()->prefix );
	submit_button( __( 'Download System Info', 'geminilabs-site-reviews' ), 'secondary', '', false ); ?>
</form>

<br>

<h3><?= __( 'Logging', 'geminilabs-site-reviews' ); ?></h3>
<p><?= __( "You can enable logging for debugging purposes. Log files can grow quickly, so don't leave this on.", 'geminilabs-site-reviews' ); ?></p>

<?php $logging_enabled = $db->getOption( '', '', 'logging' ); ?>

<?php if( $logging_enabled == 1 ) : ?>

<textarea rows="13" id="log-file" class="glsr-log" onclick="this.select()" readonly><?= $log; ?></textarea>

<form method="post" class="float-left"><?php
	wp_nonce_field( 'clear-log' );
	printf( '<input type="hidden" name="%s[action]" value="clear-log">', glsr_app()->prefix );
	submit_button( __( 'Clear Log', 'geminilabs-site-reviews' ), 'secondary', 'clear-log', false ); ?>
</form>

<form method="post"><?php
	wp_nonce_field( 'download-log' );
	printf( '<input type="hidden" name="%s[action]" value="download-log">', glsr_app()->prefix );
	submit_button( __( 'Download Log', 'geminilabs-site-reviews' ), 'secondary', '', false ); ?>
</form>

<?php endif; ?>

<form method="post" action="<?= admin_url('options.php'); ?>"><?php

	settings_fields( glsr_app()->id . '-logging' );
	do_settings_sections( glsr_app()->id . '-logging' );

	if( $logging_enabled == 1 ) {
		printf( '<input type="hidden" name="%s_logging" value="0">', glsr_app()->prefix );
		submit_button( __( 'Disable Logging', 'geminilabs-site-reviews' ) );
	}
	else {
		printf( '<input type="hidden" name="%s_logging" value="1">', glsr_app()->prefix );
		submit_button( __( 'Enable Logging', 'geminilabs-site-reviews' ) );
	}?>
</form>
