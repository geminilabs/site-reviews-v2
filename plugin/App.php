<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Container;

/**
 * @property array $mceShortcodes
 */
final class App extends Container
{
	public $defaults;
	public $file;
	public $id;
	public $name;
	public $path;
	public $post_type;
	public $prefix;
	public $taxonomy;
	public $url;
	public $version;

	public function __construct()
	{
		// hardcoded path to the plugin file
		$file = realpath( dirname( __DIR__ ) . '/site-reviews.php' );

		$data = [
			'id'      => 'Text Domain',
			'name'    => 'Plugin Name',
			'version' => 'Version',
		];

		$plugin = get_file_data( $file, $data, 'plugin' );

		$this->id      = 'geminilabs-' . $plugin['id'];
		$this->file    = $file;
		$this->name    = $plugin['name'];
		$this->path    = plugin_dir_path( $file );
		$this->prefix  = str_replace( '-', '_', $this->id );
		$this->url     = plugin_dir_url( $file );
		$this->version = $plugin['version'];

		$this->post_type = 'site-review';
		$this->taxonomy  = 'site-review-category';
	}

	/**
	 * This is the Application entry point
	 *
	 * @return void
	 */
	public function init()
	{
		$basename = plugin_basename( $this->file );

		$main   = $this->make( 'Controllers\MainController' );
		$review = $this->make( 'Controllers\ReviewController' );
		$router = $this->make( 'Router' );

		// Action Hooks
		add_action( 'plugins_loaded',                        [ $this, 'registerAddons'] );
		add_action( 'upgrader_process_complete',             [ $this, 'upgrade'], 10, 2 );
		add_action( 'admin_enqueue_scripts',                 [ $main, 'enqueueAssets'] );
		add_action( 'wp_enqueue_scripts',                    [ $main, 'enqueueAssets'] );
		add_action( 'admin_menu',                            [ $main, 'registerMenuCount'] );
		add_action( 'add_meta_boxes',                        [ $main, 'registerMetaBox'] );
		add_action( 'admin_enqueue_scripts',                 [ $main, 'registerPointers'], 13 );
		add_action( 'init',                                  [ $main, 'registerPostType'], 8 );
		add_action( 'admin_init',                            [ $main, 'registerSettings'] );
		add_action( 'admin_init',                            [ $main, 'registerShortcodeButtons'] );
		add_action( 'init',                                  [ $main, 'registerShortcodes'] );
		add_action( 'admin_menu',                            [ $main, 'registerSubMenus'] );
		add_action( 'init',                                  [ $main, 'registerTaxonomy'], 9 );
		add_action( 'init',                                  [ $main, 'registerTextdomain'] );
		add_action( 'widgets_init',                          [ $main, 'registerWidgets'] );
		add_action( 'post_submitbox_misc_actions',           [ $main, 'renderMetaBoxPinned'] );
		add_action( 'edit_form_after_title',                 [ $main, 'renderReview'] );
		add_action( 'edit_form_top',                         [ $main, 'renderReviewNotice'] );
		add_action( 'media_buttons',                         [ $main, 'renderTinymceButton'] );
		add_action( 'admin_action_approve',                  [ $review, 'approve'] );
		add_action( 'admin_print_scripts-post.php',          [ $review, 'modifyAutosave'], 999 );
		add_action( 'current_screen',                        [ $review, 'modifyFeatures'] );
		add_action( 'admin_menu',                            [ $review, 'removeMetaBoxes'] );
		add_action( 'admin_action_revert',                   [ $review, 'revert'] );
		add_action( 'admin_init',                            [ $review, 'setPermissions'], 999 );
		add_action( 'admin_action_unapprove',                [ $review, 'unapprove'] );
		add_action( "wp_ajax_{$this->prefix}_action",        [ $router, 'routeAjaxRequests'] );
		add_action( "wp_ajax_nopriv_{$this->prefix}_action", [ $router, 'routeAjaxRequests'] );
		add_action( 'admin_init',                            [ $router, 'routePostRequests'] );
		add_action( 'admin_init',                            [ $router, 'routeWebhookRequests'] );

		// Filter Hooks
		add_filter( "plugin_action_links_{$basename}", [ $main, 'registerActionLinks'] );
		add_filter( 'dashboard_glance_items',          [ $main, 'registerDashboardGlanceItems'] );
		add_filter( 'post_row_actions',                [ $main, 'registerRowActions'], 10, 2 );
		add_filter( 'wp_editor_settings',              [ $review, 'modifyEditor' ] );
		add_filter( 'the_editor',                      [ $review, 'modifyEditorTextarea'] );
		add_filter( 'ngettext',                        [ $review, 'modifyStatusFilter'], 10, 5 );
		add_filter( 'post_updated_messages',           [ $review, 'modifyUpdateMessages'] );
		add_filter( 'bulk_post_updated_messages',      [ $review, 'modifyUpdateMessagesBulk'], 10, 2 );
	}

	/**
	 * Runs on plugin activation
	 *
	 * @return void
	 */
	public function activate()
	{
		$this->updateVersion();

		update_option( "{$this->prefix}_logging", 0 );

		$this->make( 'Database' )->setDefaults();

		// Schedule session purge
		if( !wp_next_scheduled( 'site-reviews/schedule/session/purge' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'site-reviews/schedule/session/purge' );
		}
	}

	/**
	 * Runs on plugin deactivation
	 *
	 * @return void
	 */
	public function deactivate()
	{
		$events = ['site-reviews/schedule/session/purge'];

		foreach( $events as $event ) {
			wp_unschedule_event( wp_next_scheduled( $event ), $event );
		}
	}

	/**
	 * Get the default settings
	 *
	 * @return array
	 */
	public function getDefaults()
	{
		if( !$this->defaults ) {
			$this->defaults = $this->make( 'Settings' )->getSettings();

			// Allow addons to modify the default settings
			$this->defaults = apply_filters( 'site-reviews/addon/defaults', $this->defaults );
		}

		return $this->defaults;
	}

	/**
	 * Verify permissions
	 *
	 * @return bool
	 */
	public function hasPermission()
	{
		return current_user_can( 'customize' );
	}

	/**
	 * Register available add-ons
	 *
	 * @return void
	 */
	public function registerAddons()
	{
		do_action( 'site-reviews/addon/register', $this );
	}

	/**
	 * Update the plugin versions in the database
	 *
	 * @param string $current
	 *
	 * @return void
	 */
	public function updateVersion( $current = '' )
	{
		if( empty( $current ) ) {
			$current = get_option( "{$this->prefix}_version" );
		}

		if( version_compare( $current, $this->version, '<' ) ) {
			update_option( "{$this->prefix}_version", $this->version );
			update_option( "{$this->prefix}_version_upgraded_from", $current );
		}
	}

	/**
	 * Runs on plugin upgrade
	 *
	 * @param mixed $upgrader
	 *
	 * @return void
	 */
	public function upgrade( $upgrader, array $data )
	{
		if( $data['action'] != 'update'
			|| $data['type'] != 'plugin'
			|| !in_array( plugin_basename( $this->file ), $data['packages'] )
		)return;

		$version = get_option( "{$this->prefix}_version" );

		if( version_compare( $version, '2.0.0', '<' ) ) {

			$upgrade = $this->make( 'Upgrade' );

			$upgrade->sidebarWidgets_200();
			$upgrade->themeMods_200();
			$upgrade->widgetSiteReviews_200();
			$upgrade->widgetSiteReviewsForm_200();
			$upgrade->yesNo_200();
		}

		$this->updateVersion( $version );
	}
}
