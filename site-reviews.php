<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝
 *
 * Plugin Name: Site Reviews
 * Plugin URI:  https://wordpress.org/plugins/site-reviews
 * Description: Receive and display site reviews
 * Version:     2.5.2
 * Author:      Paul Ryley
 * Author URI:  http://geminilabs.io
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: site-reviews
 * Domain Path: languages
 */

defined( 'WPINC' ) || die;

require_once __DIR__ . '/activate.php';
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/compatibility.php';

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Providers\MainProvider;

$app = App::load();

$app->register( new MainProvider );

register_activation_hook( __FILE__, array( $app, 'activate' ));
register_deactivation_hook( __FILE__, array( $app, 'deactivate' ));

$app->init();

/**
 * Global helper to return $app
 *
 * @return App
 */
function glsr_app() {
	return App::load();
}

/**
 * Global helper to debug variables
 *
 * @return void
 */
function glsr_debug() {
	call_user_func_array([ App::load()->make( 'Log\Logger' ), 'display' ], func_get_args());
}

/**
 * Global helper to get a plugin option
 *
 * @return mixed
 */
function glsr_get_option( $option_path = '', $fallback = '' ) {
	return App::load()->make( 'Helper' )->get( 'option', $option_path, $fallback );
}

/**
 * Global helper to get all plugin options
 *
 * @return array
 */
function glsr_get_options() {
	return App::load()->make( 'Helper' )->get( 'options' );
}

/**
 * Global helper to get a single review
 *
 * @return null|object
 */
function glsr_get_review( $post_id ) {
	return App::load()->make( 'Helper' )->get( 'review', $post_id );
}

/**
 * Global helper to get an array of reviews
 *
 * @return array
 */
function glsr_get_reviews( array $args = [] ) {
	return App::load()->make( 'Helper' )->get( 'reviews', $args );
}

/**
 * Global helper to resolve a class instance where $app is not accessible
 *
 * @return class
 */
function glsr_resolve( $alias ) {
	return App::load()->make( $alias );
}

/**
 * register_taxonomy() 'meta_box_cb' callback
 *
 * This function prevents the taxonomy object from containing class recursion
 *
 * @return void
 */
function glsr_categories_meta_box( $post, $box ) {
	App::load()->make( 'Controllers\MainController' )->renderTaxonomyMetabox( $post, $box );
}

/**
 * get_current_screen() is unreliable because it is defined on most admin pages, but not all.
 *
 * @return WP_Screen|null
 */
function glsr_current_screen() {
	global $current_screen;
	return isset( $current_screen ) ? $current_screen : null;
}
