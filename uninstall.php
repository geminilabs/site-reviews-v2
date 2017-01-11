<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || die;

require_once __DIR__ . '/site-reviews.php';

$majorVersion = explode( '.', glsr_app()->version )[0];

// remove plugin and widget options
$options = [
	sprintf( '%s-v%d', glsr_app()->prefix, $majorVersion ),
	sprintf( 'widget_%s_site-reviews', glsr_app()->id ),
	sprintf( 'widget_%s_site-reviews-form', glsr_app()->id ),
];

foreach( $options as $option ) {
	delete_option( $option );
}

glsr_resolve( 'Session' )->deleteSessions();
