<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv2 or later
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Database;

use WP_Query;

trait Reviews
{
	protected $app;

	/**
	 * @return int
	 */
	abstract public function getCurrentPageNumber();

	/**
	 * @param string $path
	 * @param mixed  $fallback
	 * @param string $suffix
	 *
	 * @return mixed
	 */
	abstract public function getOption( $path = '', $fallback = '', $suffix = 'settings' );

	/**
	 * Save a review to the database
	 *
	 * @param string $review_id
	 * @param bool   $update
	 *
	 * @return int|bool
	 */
	public function createReview( $review_id, array $meta, $update = false )
	{
		$post_id = $this->getReviewId( $review_id );

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
			'post_name'      => $meta['site_name'] . str_replace( ['review_', '_'], '-', $review_id ),
			'post_status'    => 'publish',
			'post_title'     => wp_strip_all_tags( $meta['title'] ),
			'post_type'      => $this->app->post_type,
		];

		if( $this->getOption( 'general.require.approval', false ) && $meta['site_name'] == 'local' ) {
			$post_data['post_status'] = 'pending';
		}

		$post_id = wp_insert_post( $post_data, true );

		if( is_wp_error( $post_id ) ) {
			glsr_resolve( 'Log/Logger' )->error( sprintf( '%s (%s)', $post_id->get_error_message(), $review_id ) );

			return false;
		}

		// add post_meta
		foreach( $meta as $field => $value ) {
			update_post_meta( $post_id, $field, $value );
		}

		return $post_id;
	}

	/**
	 * Delete reviews based on an array of review_id meta values
	 *
	 * @return void
	 */
	public function deleteReviews( array $review_ids = [] )
	{
		global $wpdb;

		foreach( $review_ids as $review_id ) {

			$post_id = $this->getReviewId( $review_id );

			if( !empty( $post_id ) ) {
				wp_delete_post( $post_id, true );
			}
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
	 * @param string $review_id
	 *
	 * @return int|null
	 */
	public function getReviewId( $review_id )
	{
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare(
			"SELECT p.ID " .
			"FROM {$wpdb->posts} AS p " .
			"INNER JOIN {$wpdb->postmeta} AS m1 ON p.ID = m1.post_id " .
			"WHERE p.post_type = '%s' " .
				"AND m1.meta_key = 'review_id' " .
				"AND m1.meta_value = '%s'",
			$this->app->post_type,
			$review_id
		));
	}

	/**
	 * Gets an array of all saved Review IDs
	 *
	 * @param string $siteName
	 *
	 * @return array
	 */
	public function getReviewIds( $siteName )
	{
		global $wpdb;

		$ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT m1.meta_value AS review_id " .
			"FROM {$wpdb->posts} AS p " .
			"INNER JOIN {$wpdb->postmeta} AS m1 ON p.ID = m1.post_id " .
			"INNER JOIN {$wpdb->postmeta} AS m2 ON p.ID = m2.post_id " .
			"WHERE p.post_type = '%s' " .
				"AND m1.meta_key = 'review_id' " .
				"AND m2.meta_key = 'site_name' " .
				"AND m2.meta_value = '%s'",
			$this->app->post_type,
			$siteName
		));

		return array_keys( array_flip( $ids ) );
	}

	/**
	 * Get array of meta values for all of a post_type
	 *
	 * @param string $key
	 * @param string $status
	 *
	 * @return array
	 *
	 * @todo refactor to input array of multiple key/status/type values
	 */
	public function getReviewMeta( $key = '', $status = 'publish' )
	{
		if( empty( $key ) ) {
			return [];
		}

		global $wpdb;

		if( $status == 'all' || empty( $status ) ) {
			$status = get_post_stati( ['exclude_from_search' => false ] );
		}

		$status = array_map( function( $ps ) { return "p.post_status = '{$ps}'"; }, (array) $status );
		$status = implode( ' OR ', $status );

		return $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm " .
			"LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id " .
			"WHERE pm.meta_key = '%s' " .
				"AND p.post_type = '%s' " .
				"AND ({$status}) " .
			"ORDER BY pm.meta_value",
			$key,
			$this->app->post_type
		));
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

		$terms = $this->normalizeTerms( $category );

		// 2.build the SQL query string

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

		return new WP_Query([
			'meta_key'       => 'pinned',
			'meta_query'     => $meta_query,
			'order'          => 'DESC',
			'orderby'        => "meta_value $order_by",
			'paged'          => $pagination ? $this->getCurrentPageNumber() : 1,
			'post_status'    => 'publish',
			'post_type'      => $this->app->post_type,
			'posts_per_page' => $count ? $count : -1,
		]);
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

		// @todo store review type labels
		array_walk( $types, function( &$value, $key ) {
			$value = sprintf( '%s reviews', ucfirst( $key ) );
		});

		return $types;
	}

	/**
	 * Reverts a review title, date, and content to the originally submitted values
	 *
	 * @param string $postId
	 *
	 * @return int|bool
	 */
	public function revertReview( $postId )
	{
		$post = get_post( $postId );

		if( !isset( $post->post_type ) || $post->post_type != $this->app->post_type ) {
			return false;
		}

		delete_post_meta( $post->ID, '_edit_last' );

		return wp_update_post([
			'ID'           => $post->ID,
			'post_content' => get_post_meta( $post->ID, 'content', true ),
			'post_date'    => get_post_meta( $post->ID, 'date', true ),
			'post_title'   => get_post_meta( $post->ID, 'title', true ),
		]);
	}
}
