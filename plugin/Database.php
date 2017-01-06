<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Database\Contract;
use GeminiLabs\SiteReviews\Database\Options;
use WP_Query;

class Database implements Contract
{
	use Options;

	/**
	 * @var App
	 */
	protected $app;

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * Save a review to the database
	 *
	 * @param string $metaReviewId
	 * @param bool   $update
	 *
	 * @return int|bool
	 */
	public function createReview( $metaReviewId, array $meta, $update = false )
	{
		$post_id = $this->getReviewId( $metaReviewId );

		if( !empty( $post_id ) && !$update ) {
			return $post_id;
		}

		// make sure we set post_meta fallback defaults
		$meta = wp_parse_args( $meta, [
			'author'     => '',
			'avatar'     => '',
			'content'    => '',
			'date'       => get_date_from_gmt( gmdate( 'Y-m-d H:i:s' )),
			'email'      => '',
			'ip_address' => '',
			'pinned'     => false,
			'rating'     => '',
			'review_id'  => '',
			'site_name'  => 'local',
			'title'      => '',
			'url'        => '',
		]);

		$post_data = [
			'comment_status' => 'closed',
			'ID'             => $post_id ? $post_id : 0,
			'ping_status'    => 'closed',
			'post_content'   => $meta['content'],
			'post_date'      => $meta['date'],
			'post_name'      => $meta['site_name'] . str_replace( ['review_', '_'], '-', $metaReviewId ),
			'post_status'    => 'publish',
			'post_title'     => wp_strip_all_tags( $meta['title'] ),
			'post_type'      => $this->app->post_type,
		];

		if( $this->getOption( 'general.require.approval', false ) && $meta['site_name'] == 'local' ) {
			$post_data['post_status'] = 'pending';
		}

		$post_id = wp_insert_post( $post_data, true );

		if( is_wp_error( $post_id ) ) {
			glsr_resolve( 'Log/Logger' )->error( sprintf( '%s (%s)', $post_id->get_error_message(), $metaReviewId ) );

			return false;
		}

		// add post_meta
		foreach( $meta as $field => $value ) {
			update_post_meta( $post_id, $field, $value );
		}

		return $post_id;
	}

	/**
	 * Delete review based on a review_id meta value
	 *
	 * @param string $metaReviewId
	 *
	 * @return void
	 */
	public function deleteReview( $metaReviewId )
	{
		$postId = $this->getReviewId( $metaReviewId );

		if( !empty( $postId ) ) {
			wp_delete_post( $postId, true );
		}
	}

	/**
	 * @param string $metaKey
	 * @param string $metaValue
	 *
	 * @return int|array
	 */
	public function getReviewCount( $metaKey = '', $metaValue = '' )
	{
		if( !$metaKey ) {
			return (array) wp_count_posts( $this->app->post_type );
		}

		$counts = wp_cache_get( $this->app->id, $metaKey . '_count' );

		if( $counts === false ) {
			global $wpdb;

			$results = (array) $wpdb->get_results( $wpdb->prepare(
				"SELECT m.meta_value AS name, COUNT( * ) num_posts " .
				"FROM {$wpdb->posts} AS p " .
				"INNER JOIN {$wpdb->postmeta} AS m ON p.ID = m.post_id " .
				"WHERE p.post_type = '%s' " .
					"AND m.meta_key = '%s' " .
				"GROUP BY name",
				$this->app->post_type,
				$metaKey
			));

			$counts = [];

			foreach( $results as $site ) {
				$counts[ $site->name ] = $site->num_posts;
			}

			wp_cache_set( $this->app->id, $counts, $metaKey . '_count' );
		}

		if( !$metaValue ) {
			return $counts;
		}

		return isset( $counts[ $metaValue ] ) ? $counts[ $metaValue ] : 0;
	}

	/**
	 * Get the review post ID from the review_id meta value
	 *
	 * @param string $metaReviewId
	 *
	 * @return int
	 */
	public function getReviewId( $metaReviewId )
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT p.ID " .
			"FROM {$wpdb->posts} AS p " .
			"INNER JOIN {$wpdb->postmeta} AS m1 ON p.ID = m1.post_id " .
			"WHERE p.post_type = '%s' " .
				"AND m1.meta_key = 'review_id' " .
				"AND m1.meta_value = '%s'",
			$this->app->post_type,
			$metaReviewId
		);

		return intval( $wpdb->get_var( $query ) );
	}

	/**
	 * Gets an array of all saved review IDs by review type
	 *
	 * @param string $reviewType
	 *
	 * @return array
	 */
	public function getReviewIds( $reviewType )
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT m1.meta_value AS review_id " .
			"FROM {$wpdb->posts} AS p " .
			"INNER JOIN {$wpdb->postmeta} AS m1 ON p.ID = m1.post_id " .
			"INNER JOIN {$wpdb->postmeta} AS m2 ON p.ID = m2.post_id " .
			"WHERE p.post_type = '%s' " .
				"AND m1.meta_key = 'review_id' " .
				"AND m2.meta_key = 'site_name' " .
				"AND m2.meta_value = '%s'",
			$this->app->post_type,
			$reviewType
		);

		return array_keys( array_flip( $wpdb->get_col( $query )));
	}

	/**
	 * Get array of meta values for all of a post_type
	 *
	 * @param string|array $keys
	 * @param string       $status
	 *
	 * @return array
	 */
	public function getReviewMeta( $keys, $status = 'publish' )
	{
		global $wpdb;

		$query = $this->app->make( 'Query' );

		if( $status == 'all' || empty( $status )) {
			$status = get_post_stati( ['exclude_from_search' => false ] );
		}

		$keys   = $query->buildSqlOr( $keys, "pm.meta_key = '%s'" );
		$status = $query->buildSqlOr( $status, "p.post_status = '%s'" );

		$query = $wpdb->prepare(
			"SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm " .
			"LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id " .
			"WHERE p.post_type = '%s' " .
				"AND ({$keys}) " .
				"AND ({$status}) " .
			"ORDER BY pm.meta_value",
			$this->app->post_type
		);

		return $wpdb->get_col( $query );
	}

	/**
	 * Gets a WP_Query object of saved Reviews
	 *
	 * @return WP_Query
	 */
	public function getReviews( array $args = [] )
	{
		$defaults = [
			'category'   => '',
			'count'      => '10',
			'order_by'   => 'date',
			'pagination' => false,
			'rating'     => '5',
			'site_name'  => '',
		];

		$args = shortcode_atts( $defaults, $args );

		extract( $args );

		if( !empty( $site_name ) && $site_name != 'all' ) {
			$meta_query[] = [
				'key'   => 'site_name',
				'value' => $site_name,
			];
		}

		$meta_query[] = [
			'key'     => 'rating',
			'value'   => $rating,
			'compare' => '>=',
		];

		$query = [
			'meta_key'       => 'pinned',
			'meta_query'     => $meta_query,
			'order'          => 'DESC',
			'orderby'        => "meta_value $order_by",
			'paged'          => $pagination ? $this->app->make( 'Query' )->getPaged() : 1,
			'post_status'    => 'publish',
			'post_type'      => $this->app->post_type,
			'posts_per_page' => $count ? $count : -1,
			'tax_query'      => $this->app->make( 'Query' )->buildTerms( $this->normalizeTerms( $category ) ),
		];

		return new WP_Query( $query );
	}

	/**
	 * Gets the review types (default type is "local")
	 *
	 * @return array
	 */
	public function getReviewTypes()
	{
		global $wpdb;

		$types = $wpdb->get_col(
			"SELECT DISTINCT(meta_value) FROM $wpdb->postmeta WHERE meta_key = 'site_name' ORDER BY meta_value ASC"
		);

		$types = array_flip( $types );

		$labels = $this->app->make( 'Strings' )->review_types();

		array_walk( $types, function( &$value, $key ) use( $labels ) {
			$value = array_key_exists( $key, $labels )
				? $labels[ $key ]
				: sprintf( '%s reviews', ucfirst( $key ));
		});

		return $types;
	}

	/**
	 * Get an array of taxonomy terms
	 *
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	public function getTerms( $taxonomy = '', array $args = [] )
	{
		!empty( $taxonomy ) ?: $taxonomy = $this->app->taxonomy;

		$terms = get_terms( $taxonomy, wp_parse_args( $args, [
			'fields'     => 'id=>name',
			'hide_empty' => false,
		]));

		return is_array( $terms ) ? $terms : [];
	}

	/**
	 * Normalize a string of comma-separated terms into an array
	 *
	 * @param string $terms
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	public function normalizeTerms( $terms, $taxonomy = '' )
	{
		!empty( $taxonomy ) ?: $taxonomy = $this->app->taxonomy;

		$terms = array_map( 'trim', explode( ',', $terms ) );
		$terms = array_map( function( $term ) use( $taxonomy ) {

			!is_numeric( $term ) ?: $term = intval( $term );

			$term = term_exists( $term, $taxonomy );

			if( isset( $term['term_id'] ) ) {
				return intval( $term['term_id'] );
			}

		}, $terms );

		return array_filter( $terms );
	}

	/**
	 * Reverts a review title, date, and content to the originally submitted values
	 *
	 * @param string $postId
	 *
	 * @return int
	 */
	public function revertReview( $postId )
	{
		$post = get_post( $postId );

		if( !isset( $post->post_type ) || $post->post_type != $this->app->post_type ) {
			return 0;
		}

		delete_post_meta( $post->ID, '_edit_last' );

		return wp_update_post([
			'ID'           => $post->ID,
			'post_content' => get_post_meta( $post->ID, 'content', true ),
			'post_date'    => get_post_meta( $post->ID, 'date', true ),
			'post_title'   => get_post_meta( $post->ID, 'title', true ),
		]);
	}

	/**
	 * Set the default settings
	 *
	 * @return array
	 */
	public function setDefaults( array $args = [] )
	{
		$defaults = [
			'data'   => null,
			'merge'  => true,
			'update' => true,
		];

		$args = shortcode_atts( $defaults, $args );

		$currentSettings = $args['merge']
			? get_option( "{$this->app->prefix}_settings", [] )
			: [];

		$currentSettings = $this->removeEmptyValuesFrom( $currentSettings );
		$defaultSettings = [];

		$args['data'] ?: $args['data'] = $this->app->getDefaults();

		foreach( $args['data'] as $path => $value ) {
			// Don't save the default selector values as they are used anyway by default.
			if( !!$args['update'] && strpos( $path, '.selectors.' ) !== false ) {
				$value = '';
			}

			$defaultSettings = $this->convertPathToArray( $path, $value, $defaultSettings );
		}

		$settings = array_replace_recursive( $defaultSettings, $currentSettings );

		if( $args['update'] ) {
			update_option( "{$this->app->prefix}_settings", $settings );
		}

		return $settings;
	}

	/**
	 * Set one or more taxonomy terms to a post
	 *
	 * @param int    $post_id
	 * @param string $terms
	 * @param string $taxonomy
	 *
	 * @return void
	 */
	public function setTerms( $post_id, $terms, $taxonomy = '' )
	{
		!empty( $taxonomy ) ?: $taxonomy = $this->app->taxonomy;

		$terms = $this->normalizeTerms( $terms, $taxonomy );

		if( !empty( $terms ) ) {
			wp_set_object_terms( $post_id, $terms, $taxonomy );
		}
	}
}
