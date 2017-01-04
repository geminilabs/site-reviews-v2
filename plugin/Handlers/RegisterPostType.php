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

	/**
	 * @var string
	 */
	protected $post_type;

	public function __construct( App $app )
	{
		$this->app = $app;
		$this->db  = $app->make( 'Database' );
	}

	public function handle( Command $command )
	{
		extract( $command->args );

		if( in_array( $post_type, get_post_types(['_builtin' => true ]) ) )return;

		$this->columns   = $columns;
		$this->post_type = $post_type;

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

		add_action( 'restrict_manage_posts',                   [ $this, 'printColumnFilters'] );
		add_action( "manage_{$post_type}_posts_custom_column", [ $this, 'printColumnValues'] );
		add_action( 'pre_get_posts',                           [ $this, 'setColumnQuery'] );

		add_filter( "manage_{$post_type}_posts_columns",         [ $this, 'modifyColumns'] );
		add_filter( "manage_edit-{$post_type}_sortable_columns", [ $this, 'modifyColumnsSortable'] );
	}

	/**
	 * Creates the custom post_type columns
	 *
	 * @return array
	 *
	 * @filter manage_{$post_type}_posts_columns
	 */
	public function modifyColumns( array $columns )
	{
		$this->columns = ['cb' => ''] + $this->columns;

		array_walk( $this->columns, function( &$value, $key ) use ( $columns ) {
			if( array_key_exists( $key, $columns ) && empty( $value ) ) {
				$value = $columns[ $key ];
			}
			else if( $key === 'sticky' ) {
				global $wp_version;

				// wrap in <span> so we can replace with a dashicon in CSS @media
				$value = "<span class=\"pinned-icon\">{$value}</span>";

				// WP < 4.4 support
				if( version_compare( $wp_version, '4.4', '<' ) ) {
					$value .= file_get_contents( "{$this->app->path}assets/img/pinned.svg" );
				}
			}
		});

		$sites = $this->db->getReviewMeta( 'site_name' );

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
	public function modifyColumnsSortable( array $columns )
	{
		$columns['author'] = 'author';
		$columns['site']   = 'site_name';
		$columns['stars']  = 'rating';
		$columns['sticky'] = 'pinned';

		return $columns;
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

		if( $post_type !== $this->post_type )return;

		$status = filter_input( INPUT_GET, 'post_status' );
		$status ?: $status = 'publish';

		$ratings = $this->db->getReviewMeta( 'rating', $status );
		$sites   = $this->db->getReviewMeta( 'site_name', $status );

		$this->renderFilterRatings( $ratings );
		$this->renderFilterSites( $sites );
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
		global $post, $wp_version;

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

			case 'author':
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

	/**
	 * Sets the WP_Query
	 *
	 * @return void
	 *
	 * @action pre_get_posts
	 */
	public function setColumnQuery( WP_Query $query )
	{
		if( !$this->hasPermission( $query ) )return;

		$this->setMeta( $query )->setOrderby( $query );
	}

	/**
	 * @return bool
	 */
	protected function hasPermission( WP_Query $query )
	{
		global $pagenow;

		return !( !is_admin()
			|| !$query->is_main_query()
			|| $query->query['post_type'] != $this->post_type
			|| $pagenow != 'edit.php'
		);
	}

	/**
	 * @param array $ratings
	 *
	 * @return void
	 */
	protected function renderFilterRatings( $ratings )
	{
		if( empty( $ratings ) || apply_filters( 'site-reviews/disable/filter/ratings', false ) )return;

		$ratings = array_flip( array_reverse( $ratings ) );

		array_walk( $ratings, function( &$value, $key ) {
			$label = _n( '%s star', '%s stars', $key, 'site-reviews' );
			$value = sprintf( $label, $key );
		});

		$ratings = [ __( 'All ratings', 'site-reviews' ) ] + $ratings;

		printf( '<label class="screen-reader-text" for="rating">%s</label>', __( 'Filter by rating', 'site-reviews' ) );

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
	protected function renderFilterSites( $sites )
	{
		if( empty( $sites ) || apply_filters( 'site-reviews/disable/filter/sites', false ) )return;

		$sites = array_combine( $sites, array_map( 'ucfirst', $sites ) );
		$sites = [ __( 'All types', 'site-reviews' ) ] + $sites;

		if( isset( $sites['local'] ) ) {
			$sites['local'] = __( 'Local Review', 'site-reviews' );
		}

		printf( '<label class="screen-reader-text" for="site_name">%s</label>', __( 'Filter by type', 'site-reviews' ) );

		$this->app->make( 'Html' )->renderPartial( 'filterby', [
			'name'   => 'site_name',
			'values' => $sites,
		]);
	}

	/**
	 * Modifies the WP_Query meta_query value
	 *
	 * @return self
	 */
	protected function setMeta( WP_Query $query )
	{
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

		return $this;
	}

	/**
	 * Modifies the WP_Query orderby value
	 *
	 * @return self
	 */
	protected function setOrderby( WP_Query $query )
	{
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

		return $this;
	}
}
