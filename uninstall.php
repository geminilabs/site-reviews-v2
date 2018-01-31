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

if( !glsr_version_check() )return;

$majorVersion = explode( '.', glsr_app()->version );

// remove plugin and widget options
$options = array(
	sprintf( '%s-v%d', glsr_app()->prefix, array_shift( $majorVersion )),
	sprintf( 'widget_%s_site-reviews', glsr_app()->id ),
	sprintf( 'widget_%s_site-reviews-form', glsr_app()->id ),
);

foreach( $options as $option ) {
	delete_option( $option );
}

glsr_resolve( 'Session' )->deleteAllSessions();
