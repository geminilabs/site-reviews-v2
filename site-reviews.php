<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝
 *
 * Plugin Name: Site Reviews
 * Plugin URI:  https://wordpress.org/plugins/site-reviews
 * Description: Receive and display site reviews
 * Version:     1.2.1
 * Author:      Paul Ryley
 * Author URI:  http://geminilabs.io
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: site-reviews
 */

defined( 'WPINC' ) || die;

require_once __DIR__ . '/activate.php';
require_once __DIR__ . '/autoload.php';

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Providers\MainProvider;

$app = App::load();

$app->register( new MainProvider );

register_activation_hook( __FILE__, array( $app, 'activate' ) );
register_deactivation_hook( __FILE__, array( $app, 'deactivate' ) );

$app->init();

// Global helper to return $app
function glsr_app() {
	return App::load();
}

// Global helper to resolve a class instance where $app is not accessible
function glsr_resolve( $alias ) {
	return App::load()->make( $alias );
}

// Wordpress 4.0-4.2 support
if( !function_exists( 'wp_roles' ) ) {
	function wp_roles() {
		global $wp_roles;
		isset( $wp_roles ) ?: $wp_roles = new WP_Roles;
		return $wp_roles;
	}
}

// Wordpress 4.0-4.2 support
if( !function_exists( 'get_avatar_url' ) ) {
	function get_avatar_url( $id_or_email, $args = null ) {
		isset( $args['size'] ) ?: $args['size'] = 96;
		isset( $args['default'] ) ?: $args['default'] = 'mystery';
		$avatar = get_avatar( $id_or_email, $args['size'], $args['default'] );
		$dom = new \DOMDocument;
		$dom->loadHTML( $avatar );
		return $dom->getElementsByTagName( 'img' )->item(0)->getAttribute( 'src' );
	}
}
