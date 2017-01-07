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

$options = [
	'logging',
	'settings',
	'version',
	'version_upgraded_from',
];

foreach( $options as $option ) {
	delete_option( glsr_app()->prefix . "_{$option}" );
}

glsr_resolve( 'Session' )->deleteSessions();
