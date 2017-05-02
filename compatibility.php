<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

defined( 'WPINC' ) || die;

// Wordpress 4.0-4.2 support
if( !function_exists( 'wp_roles' )) {
	function wp_roles() {
		global $wp_roles;
		isset( $wp_roles ) ?: $wp_roles = new WP_Roles;
		return $wp_roles;
	}
}

// Wordpress 4.0-4.2 support
if( !function_exists( 'get_avatar_url' )) {
	function get_avatar_url( $id_or_email, $args = null ) {
		isset( $args['size'] ) ?: $args['size'] = 96;
		isset( $args['default'] ) ?: $args['default'] = 'mystery';
		$avatar = get_avatar( $id_or_email, $args['size'], $args['default'] );
		$dom = new \DOMDocument;
		$dom->loadHTML( $avatar );
		return $dom->getElementsByTagName( 'img' )->item(0)->getAttribute( 'src' );
	}
}

// Wordpress 4.0 support
add_filter( 'script_loader_src', function( $src, $handle ) {
	global $wp_version;
	if( version_compare( $wp_version, '4.1', '<' )
		&& strpos( $handle, '/google-recaptcha' ) !== false
		&& strpos( $src, ' async defer ' ) === false ) {
		return sprintf( "%s' async defer='defer", $src );
	}
	return $src;
}, 10, 2 );
