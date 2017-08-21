<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Commands\EnqueueAssets;
use GeminiLabs\SiteReviews\Commands\RegisterPointers;
use GeminiLabs\SiteReviews\Commands\RegisterPostType;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodeButtons;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodes;
use GeminiLabs\SiteReviews\Commands\RegisterTaxonomy;
use GeminiLabs\SiteReviews\Commands\RegisterWidgets;
use GeminiLabs\SiteReviews\Controllers\BaseController;
use GeminiLabs\SiteReviews\Strings;
use WP_Admin_Bar;
use WP_Post;

class MainController extends BaseController
{
	/**
	 * @return void
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueueAssets()
	{
		$command = new EnqueueAssets([
			'handle'  => $this->app->id,
			'path'    => $this->app->path . 'assets/',
			'url'     => $this->app->url . 'assets/',
			'version' => $this->app->version,
		]);

		$this->execute( $command );
	}

	/**
	 * @return string
	 *
	 * @filter script_loader_tag
	 */
	public function filterEnqueuedScripts( $tag, $handle )
	{
		return $handle == $this->app->id . '/google-recaptcha'
			&& glsr_get_option( 'reviews-form.recaptcha.integration' ) == 'custom'
			? str_replace( ' src=', ' async defer src=', $tag )
			: $tag;
	}

	/**
	 * Clears the log
	 *
	 * @return void
	 */
	public function postClearLog()
	{
		$this->log->clear();
		$this->notices->addSuccess( __( 'Log was cleared.', 'site-reviews' ));
	}

	/**
	 * Downloads the log
	 *
	 * @return void
	 */
	public function postDownloadLog()
	{
		$this->log->download();
	}

	/**
	 * Downloads the system info
	 *
	 * @param string $system_info
	 *
	 * @return void
	 */
	public function postDownloadSystemInfo( $system_info )
	{
		$this->app->make( 'SystemInfo' )->download( $system_info );
	}

	/**
	 * Registers the plugin action links on the plugins page
	 *
	 * @return array
	 *
	 * @filter plugin_action_links_reviews/reviews.php
	 */
	public function registerActionLinks( array $links )
	{
		$settings_url = admin_url( 'edit.php?post_type=site-review&page=settings' );

		$links[] = sprintf( '<a href="%s">%s</a>', $settings_url, __( 'Settings', 'site-reviews' ));

		return $links;
	}

	/**
	 * Adds the reviews count to the "At a Glance" Dashboard widget
	 *
	 * @return array
	 *
	 * @filter dashboard_glance_items
	 */
	public function registerDashboardGlanceItems( array $items )
	{
		$post_type = App::POST_TYPE;
		$num_posts = wp_count_posts( $post_type );

		if( !isset( $num_posts->publish ) || !$num_posts->publish ) {
			return $items;
		}

		$text = _n( '%s Review', '%s Reviews', $num_posts->publish, 'site-reviews' );
		$text = sprintf( $text, number_format_i18n( $num_posts->publish ));

		$post_type_object = get_post_type_object( $post_type );

		$items[] = $post_type_object && current_user_can( $post_type_object->cap->edit_posts )
			? sprintf( '<a class="glsr-review-count" href="edit.php?post_type=%s">%s</a>', $post_type, $text )
			: sprintf( '<span class="glsr-review-count">%s</span>', $text );

		return $items;
	}

	/**
	 * @return void
	 *
	 * @action admin_menu
	 */
	public function registerMenuCount()
	{
		global $menu, $typenow;

		$post_type = App::POST_TYPE;

		foreach( $menu as $key => $value ) {
			if( !isset( $value[2] ) || $value[2] !== "edit.php?post_type={$post_type}" )continue;

			$postCount = wp_count_posts( $post_type );

			$menu[ $key ][0] .= sprintf( ' <span class="awaiting-mod count-%d"><span class="pending-count">%s</span></span>',
				absint( $postCount->pending ),
				number_format_i18n( $postCount->pending )
			);

			if( $typenow === $post_type ) {
				$menu[ $key ][4] .= ' current';
			}

			break;
		}
	}

	/**
	 * @param string $post_type
	 *
	 * @return void
	 *
	 * @action add_meta_boxes_review
	 */
	public function registerMetaBox( $post_type )
	{
		if( $post_type != App::POST_TYPE )return;

		add_meta_box( "{$this->app->id}_assigned_to", __( 'Assigned To', 'site-reviews' ), [ $this, 'renderAssignedToMetabox'], null, 'side' );
		add_meta_box( "{$this->app->id}_review", __( 'Details', 'site-reviews' ), [ $this, 'renderMetaBox'], null, 'side' );
		add_meta_box( "{$this->app->id}_response", __( 'Respond Publicly', 'site-reviews' ), [ $this, 'renderResponseMetaBox'], null, 'normal' );
	}

	/**
	 * @return void
	 *
	 * @action admin_enqueue_scripts
	 */
	public function registerPointers()
	{
		$command = new RegisterPointers([[
			'id'       => 'glsr-pointer-pinned',
			'screen'   => App::POST_TYPE,
			'target'   => '#misc-pub-pinned',
			'title'    => __( 'Pin Your Reviews', 'site-reviews' ),
			'content'  => __( 'You can pin exceptional reviews so that they are always shown first in your widgets and shortcodes.', 'site-reviews' ),
			'position' => [
				'edge'  => 'right',  // top, bottom, left, right
				'align' => 'middle', // top, bottom, left, right, middle
			],
		]]);

		$this->execute( $command );
	}

	/**
	 * @return void
	 *
	 * @action init
	 */
	public function registerPostType()
	{
		if( !$this->app->hasPermission() )return;

		$command = new RegisterPostType([
			'single'      => __( 'Review', 'site-reviews' ),
			'plural'      => __( 'Reviews', 'site-reviews' ),
			'menu_name'   => __( 'Site Reviews', 'site-reviews' ),
			'menu_icon'   => 'dashicons-star-half',
			'public'      => false,
			'has_archive' => false,
			'show_ui'     => true,
			'labels'      => glsr_resolve( 'Strings' )->post_type_labels(),
			'columns'     => [
				'title'       => '', // empty values use the default label
				'category'    => '',
				'assigned_to' => __( 'Assigned To', 'site-reviews' ),
				'reviewer'    => __( 'Author', 'site-reviews' ),
				'type'        => __( 'Type', 'site-reviews' ),
				'stars'       => __( 'Rating', 'site-reviews' ),
				'sticky'      => __( 'Pinned', 'site-reviews' ),
				'date'        => '',
			],
		]);

		$this->execute( $command );
	}

	/**
	 * Add Approve/Unapprove links and remove Quick-edit
	 *
	 * @return array
	 *
	 * @filter post_row_actions
	 */
	public function registerRowActions( array $actions, WP_Post $post )
	{
		if( $post->post_type !== App::POST_TYPE || $post->post_status === 'trash' ) {
			return $actions;
		}

		$atts = [
			'approve' => [
				'aria-label' => esc_attr__( 'Approve this review', 'site-reviews' ),
				'href'       => wp_nonce_url( admin_url( sprintf( 'post.php?post=%s&action=approve', $post->ID )), 'approve-review_' . $post->ID ),
				'text'       => __( 'Approve', 'site-reviews' ),
			],
			'unapprove' => [
				'aria-label' => esc_attr__( 'Unapprove this review', 'site-reviews' ),
				'href'       => wp_nonce_url( admin_url( sprintf( 'post.php?post=%s&action=unapprove', $post->ID )), 'unapprove-review_' . $post->ID ),
				'text'       => __( 'Unapprove', 'site-reviews' ),
			],
		];

		$newActions = [];

		foreach( $atts as $key => $values ) {
			$newActions[ $key ] = sprintf( '<a href="%s" class="change-%s-status" aria-label="%s">%s</a>',
				$values['href'],
				App::POST_TYPE,
				$values['aria-label'],
				$values['text']
			);
		}

		// Remove Quick-edit
		unset( $actions['inline hide-if-no-js'] );

		return $newActions + $actions;
	}

	/**
	 * @return void
	 *
	 * @action admin_init
	 */
	public function registerSettings()
	{
		$optionName = $this->db->getOptionName();

		$settings = apply_filters( 'site-reviews/settings', ['logging', 'settings'] );

		foreach( $settings as $setting ) {
			register_setting( sprintf( '%s-%s', $this->app->id, $setting ), $optionName, [ $this, 'sanitizeSettings' ] );
		}

		$this->app->make( 'Settings' )->register();
	}

	/**
	 * @return void
	 *
	 * @action admin_init
	 */
	public function registerShortcodeButtons()
	{
		$site_reviews = esc_html__( 'Recent Site Reviews', 'site-reviews' );
		$site_reviews_summary = esc_html__( 'Site Reviews Summary', 'site-reviews' );
		$site_reviews_form = esc_html__( 'Submit a Site Review', 'site-reviews' );

		$command = new registerShortcodeButtons([
			'site_reviews' => [
				'title' => $site_reviews,
				'label' => $site_reviews,
			],
			'site_reviews_summary' => [
				'title' => $site_reviews_summary,
				'label' => $site_reviews_summary,
			],
			'site_reviews_form' => [
				'title' => $site_reviews_form,
				'label' => $site_reviews_form,
			],
		]);

		$this->execute( $command );
	}

	/**
	 * @return void
	 *
	 * @action init
	 */
	public function registerShortcodes()
	{
		$command = new RegisterShortcodes([
			'site_reviews',
			'site_reviews_form',
			'site_reviews_summary',
		]);

		$this->execute( $command );
	}

	/**
	 * @return void
	 *
	 * @action admin_menu
	 */
	public function registerSubMenus()
	{
		$pages = [
			'settings' => __( 'Settings', 'site-reviews' ),
			'help'     => __( 'Get Help', 'site-reviews' ),
			'addons'   => __( 'Add-Ons', 'site-reviews' ),
		];

		$pages = apply_filters( 'site-reviews/addon/submenu/pages', $pages );

		foreach( $pages as $slug => $title ) {

			$method = sprintf( 'render%sMenu', ucfirst( $slug ));

			$callback = apply_filters( 'site-reviews/addon/submenu/callback', [ $this, $method ], $slug );

			if( !is_callable( $callback ))continue;

			add_submenu_page( sprintf( 'edit.php?post_type=%s', App::POST_TYPE ), $title, $title, App::CAPABILITY, $slug, $callback );
		}
	}

	/**
	 * @return void
	 *
	 * @action init
	 */
	public function registerTaxonomy()
	{
		$command = new RegisterTaxonomy([
			'hierarchical'      => true,
			'meta_box_cb'       => 'glsr_categories_meta_box',
			'public'            => false,
			'show_admin_column' => true,
			'show_ui'           => true,
		]);

		$this->execute( $command );
	}

	/**
	 * @return void
	 *
	 * @action widgets_init
	 */
	public function registerWidgets()
	{
		$command = new RegisterWidgets([
			'site-reviews' => [
				'title'       => __( 'Recent Site Reviews', 'site-reviews' ),
				'description' => __( 'Your site’s most recent Local Reviews.', 'site-reviews' ),
				'class'       => 'glsr-widget glsr-widget-recent-reviews',
			],
			'site-reviews-form' => [
				'title'       => __( 'Submit a Site Review', 'site-reviews' ),
				'description' => __( 'A "submit a review" form for your site.', 'site-reviews' ),
				'class'       => 'glsr-widget glsr-widget-reviews-form',
			],
		]);

		$this->execute( $command );
	}

	/**
	 * add_submenu_page() callback
	 *
	 * @return void
	 */
	public function renderAddonsMenu()
	{
		$this->renderMenu( 'addons', [
			'addons' => __( 'Add-Ons', 'site-reviews' ),
		]);
	}

	/**
	 * register_taxonomy() 'meta_box_cb' callback
	 *
	 * @return void
	 */
	public function renderAssignedToMetabox( $post )
	{
		if( $post->post_type != App::POST_TYPE )return;

		$assignedTo = get_post_meta( $post->ID, 'assigned_to', true );
		$permalink = $this->html->renderPartial( 'link', [
			'post_id' => $assignedTo,
		]);

		wp_nonce_field( 'assigned_to', '_nonce-assigned-to', false );

		$this->render( 'edit/metabox-assigned-to', [
			'ID' => $assignedTo,
			'permalink' => $permalink,
		]);
	}

	/**
	 * add_submenu_page() callback
	 *
	 * @return void
	 */
	public function renderHelpMenu()
	{
		// allow addons to add their own help sections
		$sections = apply_filters( 'site-reviews/addon/documentation/sections', [
			'support'    => __( 'Support', 'site-reviews' ),
			'shortcodes' => __( 'Shortcodes', 'site-reviews' ),
			'hooks'      => __( 'Hooks', 'site-reviews' ),
			'helpers'    => __( 'Helper Functions', 'site-reviews' ),
		]);

		$this->renderMenu( 'help', [
			'documentation' => [
				'title'    => __( 'Documentation', 'site-reviews' ),
				'sections' => $sections,
			],
			'system' => __( 'System Info', 'site-reviews' ),
		],[
			'system_info' => $this->app->make( 'SystemInfo' ),
		]);
	}

	/**
	 * @return void
	 */
	public function renderMenu( $page, $tabs, $data = [] )
	{
		$tabs    = $this->normalizeTabs( $tabs );
		$tab     = $this->filterTab( $tabs );
		$section = $this->filterSection( $tabs, $tab );

		$defaults = [
			'page'           => $page,
			'tabs'           => $tabs,
			'tabView'        => $tab,
			'tabViewSection' => $section,
		];

		$data = apply_filters( 'site-reviews/addon/menu/data', $data, $defaults );

		$this->render( 'menu/index', wp_parse_args( $data, $defaults ));
	}

	/**
	 * add_meta_box() callback
	 *
	 * @return void
	 */
	public function renderMetaBox( WP_Post $post )
	{
		if( $post->post_type != App::POST_TYPE )return;

		$review = glsr_resolve( 'Helper' )->get( 'review', $post->ID );

		$this->render( 'edit/metabox-details', [
			'button'  => $this->getMetaboxButton( $post, $review ),
			'metabox' => $this->getMetaboxDetails( $review ),
		]);
	}

	/**
	 * @return void
	 *
	 * @action post_submitbox_misc_actions
	 */
	public function renderMetaBoxPinned()
	{
		global $post;

		if( $post->post_type != App::POST_TYPE )return;

		$pinned = get_post_meta( $post->ID, 'pinned', true );

		$this->render( 'edit/pinned', ['pinned' => $pinned ] );
	}

	/**
	 * add_meta_box() callback
	 *
	 * @return void
	 */
	public function renderResponseMetaBox( WP_Post $post )
	{
		if( $post->post_type != App::POST_TYPE )return;

		$review = glsr_resolve( 'Helper' )->get( 'review', $post->ID );

		wp_nonce_field( 'response', '_nonce-response', false );

		$this->render( 'edit/metabox-response', [
			'response' => $review->response,
		]);
	}

	/**
	 * @return void
	 *
	 * @action edit_form_after_title
	 */
	public function renderReview( WP_Post $post )
	{
		if( $post->post_type != App::POST_TYPE )return;
		if( post_type_supports( App::POST_TYPE, 'title' ))return;
		if( get_post_meta( $post->ID, 'review_type', true ) == 'local' )return;

		$this->render( 'edit/review', ['post' => $post ] );
	}

	/**
	 * @return void
	 *
	 * @action edit_form_top
	 */
	public function renderReviewNotice( WP_Post $post )
	{
		if( $post->post_type != App::POST_TYPE )return;
		if( post_type_supports( App::POST_TYPE, 'title' ))return;

		$reviewType = get_post_meta( $post->ID, 'review_type', true );

		if( $reviewType == 'local' )return;

		$this->notices->addWarning( sprintf( __( '%s reviews are read-only.', 'site-reviews' ), ucfirst( $reviewType )));
		$this->render( 'edit/notice' );
	}

	/**
	 * @return void
	 *
	 * @action wp_footer
	 */
	public function renderSchema()
	{
		echo $this->app->make( 'Schema' )->render();
	}

	/**
	 * add_submenu_page() callback
	 *
	 * @return void
	 */
	public function renderSettingsMenu()
	{
		// allow addons to add their own setting sections
		$sections = apply_filters( 'site-reviews/addon/settings/sections', [
			'general' => __( 'General', 'site-reviews' ),
			'reviews' => __( 'Reviews', 'site-reviews' ),
			'reviews-form' => __( 'Submission Form', 'site-reviews' ),
			'strings' => __( 'Translations', 'site-reviews' ),
		]);

		$this->renderMenu( 'settings', [
			'settings' => [
				'title' => __( 'Settings', 'site-reviews' ),
				'sections' => $sections,
			],
			'licenses' => __( 'Licenses', 'site-reviews' ),
		],[
			'settings' => $this->app->getDefaultSettings(),
		]);
	}

	/**
	 * register_taxonomy() 'meta_box_cb' callback
	 *
	 * @return void
	 */
	public function renderTaxonomyMetabox( $post, $box )
	{
		if( $post->post_type != App::POST_TYPE )return;

		$taxonomy = isset( $box['args']['taxonomy'] )
			? $box['args']['taxonomy']
			: App::TAXONOMY;

		$this->render( 'edit/metabox-categories', [
			'post'     => $post,
			'tax_name' => esc_attr( $taxonomy ),
			'taxonomy' => get_taxonomy( $taxonomy ),
		]);
	}

	/**
	 * Adds the shortcode button above the TinyMCE Editor on add/edit screens
	 *
	 * @return null|void
	 *
	 * @action media_buttons
	 */
	public function renderTinymceButton()
	{
		if( !glsr_current_screen() || glsr_current_screen()->base != 'post' )return;

		$shortcodes = [];

		foreach( $this->app->mceShortcodes as $shortcode => $values ) {
			if( !apply_filters( sanitize_title( $shortcode ) . '_condition', true ))continue;
			$shortcodes[ $shortcode ] = $values;
		}

		if( empty( $shortcodes ))return;

		$this->render( 'edit/tinymce', ['shortcodes' => $shortcodes ] );
	}

	/**
	 * register_setting() callback
	 * @param array $input
	 * @return array
	 */
	public function sanitizeSettings( $input )
	{
		if( !is_array( $input )) {
			$input = ['settings' => []];
		}

		$key = key( $input );
		$message = '';

		if( $key == 'logging' ) {
			$message = $input[ $key ]
				? __( 'Logging enabled.', 'site-reviews' )
				: __( 'Logging disabled.', 'site-reviews' );
		}
		else if( $key == 'settings' ) {
			$message = __( 'Settings updated.', 'site-reviews' );
		}

		$message = apply_filters( 'site-reviews/settings/notice', $message, $key );

		if( $message ) {
			$this->notices->addSuccess( $message );
		}

		$options = array_replace_recursive( $this->db->getOptions(), $input );

		if( isset( $input['settings']['strings'] )) {
			$options['settings']['strings'] = array_values( array_filter( $input['settings']['strings'] ));
			array_walk( $options['settings']['strings'], function( &$string ) {
				if( isset( $string['s2'] )) {
					$string['s2'] = wp_strip_all_tags( $string['s2'] );
				}
				if( isset( $string['p2'] )) {
					$string['p2'] = wp_strip_all_tags( $string['p2'] );
				}
			});
		}
		return $options;
	}

	/**
	 * Gets the current menu page tab section
	 *
	 * @param string $tab
	 *
	 * @return string
	 */
	protected function filterSection( array $tabs, $tab )
	{
		$section = filter_input( INPUT_GET, 'section' );

		if( !$section || !isset( $tabs[ $tab ]['sections'][ $section ] )) {
			$section = isset( $tabs[ $tab ]['sections'] )
				? key( $tabs[ $tab ]['sections'] )
				: '';
		}

		return $section;
	}

	/**
	 * Gets the current menu page tab
	 *
	 * @return string
	 */
	protected function filterTab( array $tabs )
	{
		$tab = filter_input( INPUT_GET, 'tab' );

		if( !$tab || !array_key_exists( $tab, $tabs )) {
			$tab = key( $tabs );
		}

		return $tab;
	}

	/**
	 * Gets the metabox revert button
	 *
	 * @param object $review
	 *
	 * @return string
	 */
	protected function getMetaboxButton( WP_Post $post, $review )
	{
		$modified = false;

		if( $post->post_title !== $review->title
			|| $post->post_content !== $review->content
			|| $post->post_date !== $review->date ) {
			$modified = true;
		}

		$revertUrl = wp_nonce_url(
			admin_url( sprintf( 'post.php?post=%s&action=revert', $post->ID )),
			'revert-review_' . $post->ID
		);

		return !$modified
			? sprintf( '<button id="revert" class="button button-large" disabled>%s</button>', __( 'Nothing to Revert', 'site-reviews' ))
			: sprintf( '<a href="%s" id="revert" class="button button-large">%s</a>', $revertUrl, __( 'Revert Changes', 'site-reviews' ));
	}

	/**
	 * Gets the metabox details
	 *
	 * @param object $review
	 *
	 * @return array
	 */
	protected function getMetaboxDetails( $review )
	{
		$reviewTypeFallback = empty( $review->review_type )
			? __( 'Unknown', 'site-reviews' )
			: ucfirst( $review->review_type );

		$reviewType = sprintf( __( '%s review', 'site-reviews' ),
			glsr_resolve( 'Strings' )->review_types( $review->review_type, $reviewTypeFallback )
		);

		if( $review->url ) {
			$reviewType = sprintf( '<a href="%s" target="_blank">%s</a>', $review->url, $reviewType );
		}

		$metabox = [
			__( 'Rating', 'site-reviews' )   => $this->html->renderPartial( 'star-rating', ['rating' => $review->rating] ),
			__( 'Type', 'site-reviews' )     => $reviewType,
			__( 'Date', 'site-reviews' )     => get_date_from_gmt( $review->date, 'F j, Y' ),
			__( 'Reviewer', 'site-reviews' ) => $review->author,
			__( 'Email', 'site-reviews' )    => sprintf( '<a href="mailto:%1$s">%1$s</a>', $review->email ),
			__( 'Avatar', 'site-reviews' )   => sprintf( '<img src="%s" width="96">', $review->avatar ),
		];

		return apply_filters( 'site-reviews/metabox/details', $metabox, $review );
	}

	/**
	 * Normalize the tabs array
	 *
	 * @return array
	 */
	protected function normalizeTabs( array $tabs )
	{
		foreach( $tabs as $key => $value ) {
			if( !is_array( $value )) {
				$tabs[ $key ] = ['title' => $value ];
			}
			if( $key == 'licenses' && !apply_filters( 'site-reviews/addon/licenses', false )) {
				unset( $tabs[ $key ] );
			}
		}

		return $tabs;
	}
}
