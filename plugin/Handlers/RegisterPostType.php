<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Commands\RegisterPostType as Command;
use GeminiLabs\SiteReviews\Strings;
use WP_Query;

class RegisterPostType
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $columns;

	/**
	 * @var Database
	 */
	protected $db;

	public function __construct( App $app )
	{
		$this->app = $app;
		$this->db  = $app->make( 'Database' );
	}

	public function handle( Command $command )
	{
		extract( $command->args );

		$this->columns = $columns;

		if( in_array( $post_type, get_post_types(['_builtin' => true ]) ) )return;

		$post_type = empty( $post_type )
			? sanitize_title( $single )
			: $post_type;

		$slug = empty( $slug )
			? sanitize_title( $plural )
			: $slug;

		$menu_name = empty( $menu_name )
			? $plural
			: $menu_name;

		$show_in_nav_menus = isset( $args['show_in_nav_menus'] )
			? $args['show_in_nav_menus']
			: $public;

		$show_ui = !isset( $args['show_ui'] ) ?: $args['show_ui'];

		$exclude_from_search = isset( $args['exclude_from_search'] )
			? $args['exclude_from_search']
			: !$public;

		$publicly_queryable = isset( $args['publicly_queryable'] )
			? $args['publicly_queryable']
			: $public;

		$labels['singular_name'] = $single;
		$labels['name'] = $plural;
		$labels['menu_name'] = $menu_name;

		$args = [
			'description'         => '',
			'labels'              => $labels,
			'taxonomies'          => $taxonomies,
			'supports'            => $supports,
			'map_meta_cap'        => $map_meta_cap,
			'menu_position'       => $menu_position,
			'menu_icon'           => $menu_icon,
			'has_archive'         => $has_archive,
			'public'              => $public,
			'show_in_nav_menus'   => $show_in_nav_menus,
			'show_ui'             => $show_ui,
			'exclude_from_search' => $exclude_from_search,
			'publicly_queryable'  => $publicly_queryable,
			'capabilities'        => ['create_posts' => "create_{$slug}"],
			'hierarchical'        => $hierarchical,
			'rewrite'             => $rewrite,
			'query_var'           => $query_var,
		];

		register_post_type( $post_type, $args );

		$this->performHooks( $post_type );
	}

	/**
	 * Removes the autosave functionality
	 *
	 * @return void
	 *
	 * @action admin_print_scripts-post.php
	 */
	public function disableAutosave()
	{
		if( $this->isEditLocalReviewPage() )return;

		global $post;

		if( $post->post_type == 'site-review' ) {
			wp_deregister_script( 'autosave' );
		}
	}

	/**
	 * Removes the slug metabox
	 *
	 * @return void
	 *
	 * @action admin_menu
	 */
	public function disableMetaBoxes()
	{
		remove_meta_box( 'slugdiv', 'site-review', 'advanced' );
	}

	/**
	 * Creates the custom post_type columns
	 *
	 * @return array
	 *
	 * @filter manage_{$post_type}_posts_columns
	 */
	public function manageColumns( array $columns )
	{
		$this->columns = ['cb' => ''] + $this->columns;

		array_walk( $this->columns, function( &$value, $key ) use ( $columns ) {
			if( array_key_exists( $key, $columns ) && empty( $value ) ) {
				$value = $columns[ $key ];
			}
			else if( $key === 'sticky' ) {
				// wrap in <span> so we can replace with a dashicon in CSS @media
				$value = "<span class=\"pinned-icon\">{$value}</span>";

				global $wp_version;

				// WP < 4.4 support
				if( version_compare( $wp_version, '4.4', '<' ) ) {
					$value .= file_get_contents( "{$this->app->path}assets/img/pinned.svg" );
				}
			}
		});

		$sites = $this->db->getMetaValues( 'site_name' );

		if( count( $sites ) < 1 || ( count( $sites ) == 1 && $sites[0] == 'local' ) ) {
			unset( $this->columns['site'] );
		}

		// remove all keys with null, false, or empty values
		return array_filter( $this->columns, 'strlen' );
	}

	/**
	 * Sets which custom post_type columns are sortable
	 *
	 * @return array
	 *
	 * @filter manage_edit-{$post_type}_sortable_columns
	 */
	public function manageSortableColumns( array $columns )
	{
		$columns['reviewer'] = 'author';
		$columns['site']     = 'site_name';
		$columns['stars']    = 'rating';
		$columns['sticky']   = 'pinned';

		return $columns;
	}

	/**
	 * Modifies the WP_Query meta_query value
	 *
	 * @return void
	 *
	 * @action pre_get_posts
	 */
	public function modifyColumnMetaQuery( WP_Query $query )
	{
		global $pagenow;

		if( !is_admin()
			|| !$query->is_main_query()
			|| $query->query['post_type'] != 'site-review'
			|| $pagenow != 'edit.php'
		)return;

		$meta_keys = [
			'rating',
			'site_name',
		];

		foreach( $meta_keys as $key ) {
			if( $value = filter_input( INPUT_GET, $key ) ) {
				$query->query_vars['meta_query'][] = [
					'key'   => $key,
					'value' => $value,
				];
			}
		}
	}

	/**
	 * Modifies the WP_Query orderby value
	 *
	 * @return void
	 *
	 * @action pre_get_posts
	 */
	public function modifyColumnOrderby( WP_Query $query )
	{
		global $pagenow;

		if( !is_admin()
			|| !$query->is_main_query()
			|| $query->query['post_type'] != 'site-review'
			|| $pagenow != 'edit.php'
		)return;

		$orderby = $query->get( 'orderby' );

		switch( $orderby ) {
			case 'author':
			case 'site_name':
			case 'rating':
			case 'pinned':
				$query->set( 'meta_key', $orderby );
				$query->set( 'orderby', 'meta_value' );
				break;
		}
	}

	/**
	 * Modifies the WP_Editor settings
	 *
	 * @return array
	 *
	 * @action wp_editor_settings
	 */
	public function modifyContentEditor( array $settings )
	{
		if( $this->isEditLocalReviewPage() !== true ) {
			return $settings;
		}

		return [
			'textarea_rows' => 12,
			'media_buttons' => false,
			'quicktags'     => false,
			'tinymce'       => false,
		];
	}

	/**
	 * Modify the WP_Editor html to allow autosizing without breaking the `editor-expand` script
	 *
	 * @param string $html
	 *
	 * @return string
	 *
	 * @action the_editor
	 */
	public function modifyContentEditorHtml( $html )
	{
		if( $this->isEditLocalReviewPage() !== true ) {
			return $html;
		}

		return str_replace( '<textarea', '<div id="ed_toolbar"></div><textarea', $html );
	}

	/**
	 * Customize the bulk updated messages array for this post_type
	 *
	 * return array
	 */
	public function modifyPostTypeBulkMessages( array $messages, array $counts )
	{
		$messages['site-review'] = [
			'updated'   => _n( '%s review updated.', '%s posts updated.', $counts['updated'], 'site-reviews' ),
			'locked'    => _n( '%s review not updated, somebody is editing it.', '%s reviews not updated, somebody is editing them.', $counts['locked'], 'site-reviews' ),
			'deleted'   => _n( '%s review permanently deleted.', '%s reviews permanently deleted.', $counts['deleted'], 'site-reviews' ),
			'trashed'   => _n( '%s review moved to the Trash.', '%s reviews moved to the Trash.', $counts['trashed'], 'site-reviews' ),
			'untrashed' => _n( '%s review restored from the Trash.', '%s reviews restored from the Trash.', $counts['untrashed'], 'site-reviews' ),
		];

		return $messages;
	}

	/**
	 * Customize the updated messages array for this post_type
	 *
	 * return array
	 */
	public function modifyPostTypeMessages( array $messages )
	{
		global $post;

		if( !isset( $post->ID ) || !$post->ID ) {
			return $messages;
		}

		$strings = (new Strings)->post_updated_messages();

		$restored = filter_input( INPUT_GET, 'revision' );
		$restored = $restored
			? sprintf( $strings['restored'], wp_post_revision_title( (int) $restored, false ) )
			: false;

		$scheduled_date = date_i18n( 'M j, Y @ H:i', strtotime( $post->post_date ) );

		$messages['site-review'] = [
			 1 => $strings['updated'],
			 4 => $strings['updated'],
			 5 => $restored,
			 6 => $strings['published'],
			 7 => $strings['saved'],
			 8 => $strings['submitted'],
			 9 => sprintf( $strings['scheduled'], sprintf( '<strong>%s</strong>', $scheduled_date ) ),
			10 => $strings['draft_updated'],
			50 => $strings['approved'],
			51 => $strings['unapproved'],
			52 => $strings['reverted'],
		];

		return $messages;
	}

	/**
	 * Prints the column filters
	 *
	 * @param string $post_type
	 *
	 * @return void
	 *
	 * @action restrict_manage_posts
	 */
	public function printColumnFilters( $post_type )
	{
		// WP < 4.4 support
		if( !$post_type ) {
			$screen = get_current_screen();
			$post_type = $screen->post_type;
		}

		if( $post_type !== 'site-review' )return;

		$status = filter_input( INPUT_GET, 'post_status' );
		$status = $status ? $status : 'publish';

		$ratings = $this->db->getMetaValues( 'rating', $status );

		$this->renderRatingsFilter( $ratings );

		$sites = $this->db->getMetaValues( 'site_name', $status );

		if( count( $sites ) == 1 && $sites[0] == 'local' )return;

		$this->renderSitesFilter( $sites );
	}

	/**
	 * Prints the custom column values
	 *
	 * @param string $column
	 *
	 * @return void
	 *
	 * @action manage_{$post_type}_posts_custom_column
	 */
	public function printColumnValues( $column )
	{
		global $post;
		global $wp_version;

		switch( $column ) {

			case 'slug':
				echo $post->post_name;
				break;

			case 'featured':
			case 'image':
			case 'thumbnail':

				if( has_post_thumbnail( $post->ID ) ) {
					$img = wp_get_attachment_image( get_post_thumbnail_id( $post->ID ), [96, 48] );
				}

				echo ( isset( $img ) && !empty( $img ) ) ? $img : '&mdash;';
				break;

			case 'reviewer':
				echo get_post_meta( $post->ID, 'author', true );
				break;

			case 'stars':
				$this->app->make( 'Html' )->renderPartial( 'rating', [
					'stars' => get_post_meta( $post->ID, 'rating', true ),
				]);
				break;

			case 'site':
				echo ucfirst( get_post_meta( $post->ID, 'site_name', true ) );
				break;

			case 'sticky':
				$pinned = get_post_meta( $post->ID, 'pinned', true )
					? ' pinned'
					: '';

				// WP < 4.4 support
				$fallback = version_compare( $wp_version, '4.4', '<' )
					? file_get_contents( "{$this->app->path}assets/img/pinned.svg" )
					: '';

				echo sprintf( '<i class="dashicons dashicons-sticky%s" data-id="%s">%s</i>', $pinned, $post->ID, $fallback );
				break;

			default:
				echo apply_filters( "_populate_column_{$column}", '', $post->ID );
				break;
		}
	}

	public function revertPost()
	{
		$post_id = filter_input( INPUT_GET, 'post' );

		if( !$post_id )return;

		check_admin_referer( 'revert-review_' . $post_id );

		$this->db->revertReview( $post_id );

		wp_redirect( $this->getRedirectUrl( $post_id, 52 ) );

		exit();
	}

	public function unapprovePost()
	{
		$post_id = filter_input( INPUT_GET, 'post' );

		if( !$post_id )return;

		check_admin_referer( 'unapprove-review_' . $post_id );

		$this->changePostStatus( $post_id, 'pending' );

		wp_redirect( $this->getRedirectUrl( $post_id, 51 ) );

		exit();
	}

	public function approvePost()
	{
		$post_id = filter_input( INPUT_GET, 'post' );

		if( !$post_id )return;

		check_admin_referer( 'approve-review_' . $post_id );

		$this->changePostStatus( $post_id, 'publish' );

		wp_redirect( $this->getRedirectUrl( $post_id, 50 ) );

		exit();
	}

	/**
	 * Set/persist custom permissions for the post_type
	 *
	 * return void
	 */
	public function setPermissions()
	{
		foreach( wp_roles()->roles as $role => $value ) {
			wp_roles()->remove_cap( $role, 'create_reviews' );
		}
	}

	/**
	 * Remove post_type support for all non-local reviews
	 *
	 * @todo: Move this to addons
	 *
	 * return void
	 */
	public function setPostTypeSupport()
	{
		if( $this->isEditLocalReviewPage() )return;

		remove_post_type_support( 'site-review', 'title' );
		remove_post_type_support( 'site-review', 'editor' );
	}

	/**
	 * @return null|bool
	 */
	protected function isEditLocalReviewPage()
	{
		$screen = get_current_screen();

		$action = filter_input( INPUT_GET, 'action' );
		$postId = filter_input( INPUT_GET, 'post' );

		if( $action != 'edit'
			|| $postId < 1
			|| $screen->base != 'post'
			|| $screen->post_type != 'site-review'
		)return;

		$siteName = get_post_meta( $postId, 'site_name', true );

		if( 'local' === $siteName ) {
			return true;
		}
	}

	/**
	 * @param string $post_type
	 *
	 * @return void
	 */
	protected function performHooks( $post_type = '' )
	{
		add_action( 'admin_action_approve',                    [ $this, 'approvePost'] );
		add_action( 'admin_print_scripts-post.php',            [ $this, 'disableAutosave'], 999 );
		add_action( 'admin_menu',                              [ $this, 'disableMetaBoxes'] );
		add_action( 'pre_get_posts',                           [ $this, 'modifyColumnMetaQuery'] );
		add_action( 'pre_get_posts',                           [ $this, 'modifyColumnOrderby'] );
		add_action( 'restrict_manage_posts',                   [ $this, 'printColumnFilters'] );
		add_action( "manage_{$post_type}_posts_custom_column", [ $this, 'printColumnValues'] );
		add_action( 'admin_action_revert',                     [ $this, 'revertPost'] );
		add_action( 'admin_init',                              [ $this, 'setPermissions'], 999 );
		add_action( 'current_screen',                          [ $this, 'setPostTypeSupport'] );
		add_action( 'admin_action_unapprove',                  [ $this, 'unapprovePost'] );

		add_filter( "manage_{$post_type}_posts_columns",         [ $this, 'manageColumns'] );
		add_filter( "manage_edit-{$post_type}_sortable_columns", [ $this, 'manageSortableColumns'] );
		add_filter( 'wp_editor_settings',                        [ $this, 'modifyContentEditor' ] );
		add_filter( 'the_editor',                                [ $this, 'modifyContentEditorHtml'] );
		add_filter( 'bulk_post_updated_messages',                [ $this, 'modifyPostTypeBulkMessages'], 10, 2 );
		add_filter( 'post_updated_messages',                     [ $this, 'modifyPostTypeMessages'] );
	}

	/**
	 * @return string
	 */
	protected function getRedirectUrl( $post_id, $message_index )
	{
		$referer = wp_get_referer();

		return !$referer || strpos( $referer, 'post.php' ) !== false || strpos( $referer, 'post-new.php' ) !== false
			? add_query_arg( ['message' => $message_index ], get_edit_post_link( $post_id, false ) )
			: add_query_arg( ['message' => $message_index ], remove_query_arg( ['trashed', 'untrashed', 'deleted', 'ids'], $referer ) );
	}

	/**
	 * @param array $ratings
	 *
	 * @return void
	 */
	protected function renderRatingsFilter( $ratings )
	{
		if( empty( $ratings ) )return;

		$ratings = array_flip( array_reverse( $ratings ) );

		array_walk( $ratings, function( &$value, $key ) {
			$label = _n( '%s star', '%s stars', $key, 'site-reviews' );
			$value = sprintf( $label, $key );
		});

		$ratings = [ __( 'All ratings', 'site-reviews' ) ] + $ratings;

		$this->app->make( 'Html' )->renderPartial( 'filterby', [
			'name'   => 'rating',
			'values' => $ratings,
		]);
	}

	/**
	 * @param array $sites
	 *
	 * @return void
	 */
	protected function renderSitesFilter( $sites )
	{
		if( empty( $sites ) )return;

		$sites = array_combine( $sites, array_map( 'ucfirst', $sites ) );
		$sites = [ __( 'All types', 'site-reviews' ) ] + $sites;

		if( isset( $sites['local'] ) ) {
			$sites['local'] = __( 'Local Review', 'site-reviews' );
		}

		$this->app->make( 'Html' )->renderPartial( 'filterby', [
			'name'   => 'site_name',
			'values' => $sites,
		]);
	}

	protected function changePostStatus( $post_id, $status )
	{
		return wp_update_post([
			'ID'          => $post_id,
			'post_status' => $status,
		]);
	}
}
