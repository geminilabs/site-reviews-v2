<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

defined( 'WPINC' ) || die;

if( !function_exists( 'glsr_version_check' )) {
	function glsr_version_check() {
		global $wp_version;
		return [
			'php' => version_compare( PHP_VERSION, '5.4.0', '<' ),
			'wordpress' => version_compare( $wp_version, '4.0', '<' ),
		];
	}
}

if( !function_exists( 'glsr_deactivate_plugin' )) {
	function glsr_deactivate_plugin( $plugin )
	{
		$check = glsr_version_check();

		if( !$check['php'] && !$check['wordpress'] )return;

		$plugin_name = plugin_basename( dirname( __FILE__ ) . '/site-reviews.php' );

		if( $plugin == $plugin_name ) {
			$paged  = filter_input( INPUT_GET, 'paged' );
			$s      = filter_input( INPUT_GET, 's' );
			$status = filter_input( INPUT_GET, 'plugin_status' );

			wp_safe_redirect( self_admin_url( sprintf( 'plugins.php?plugin_status=%s&paged=%s&s=%s', $status, $paged, $s )));
			die;
		}

		deactivate_plugins( $plugin_name );

		$title = __( 'The Site Reviews plugin was deactivated.', 'site-reviews' );
		$msg_1 = '';
		$msg_2 = '';

		if( $check['php'] ) {
			$msg_1 = __( 'Sorry, this plugin requires PHP version 5.4 or greater in order to work properly.', 'site-reviews' );
			$msg_2 = __( 'Please contact your hosting provider or server administrator to upgrade the version of PHP on your server (your server is running PHP version %s), or try to find an alternative plugin.', 'site-reviews' );
			$msg_2 = sprintf( $msg_2, PHP_VERSION );
		}

		// WordPress check overrides the PHP check
		if( $check['wordpress'] ) {
			$msg_1 = __( 'Sorry, this plugin requires WordPress version 4.0.0 or greater in order to work properly.', 'site-reviews' );
			$msg_2 = sprintf( '<a href="%s">%s</a>', admin_url( 'update-core.php' ), __( 'Update WordPress', 'site-reviews' ));
		}

		printf( '<div id="message" class="notice notice-error error is-dismissible"><p><strong>%s</strong></p><p>%s</p><p>%s</p></div>',
			$title,
			$msg_1,
			$msg_2
		);
	}
}

$check = glsr_version_check();

// PHP >= 5.4.0 and WordPress version >= 4.0.0 check
if( $check['php'] || $check['wordpress'] ) {
	add_action( 'activated_plugin', 'glsr_deactivate_plugin' );
	add_action( 'admin_notices', 'glsr_deactivate_plugin' );
}
