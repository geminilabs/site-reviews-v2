<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝
 *
 * Plugin Name: Site Reviews
 * Plugin URI:  https://wordpress.org/plugins/site-reviews
 * Description: Receive and display site reviews
 * Version:     2.13.2
 * Author:      Paul Ryley
 * Author URI:  https://profiles.wordpress.org/pryley#content-plugins
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: site-reviews
 * Domain Path: languages
 */

defined( 'WPINC' ) || die;

require_once __DIR__ . '/activate.php';

if( !glsr_version_check() )return;

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/compatibility.php';
require_once __DIR__ . '/helpers.php';

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Providers\MainProvider;

$app = App::load();

$app->register( new MainProvider );

register_activation_hook( __FILE__, array( $app, 'activate' ));
register_deactivation_hook( __FILE__, array( $app, 'deactivate' ));

$app->init();
