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

class Database
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Logger
	 */
	protected $log;

	public function __construct( App $app )
	{
		$this->app = $app;
		$this->log = $app->make( 'Log\Logger' );
	}

	/**
	 * Count all reviews
	 *
	 * @param string $siteName
	 *
	 * @return array
	 */
	public function countReviews( $siteName )
	{
		global $wpdb;

		$counts = wp_cache_get( $this->app->id, 'counts' );

		if( false !== $counts ) {
			return isset( $counts[ $siteName ] ) ? $counts[ $siteName ] : 0;
		}

		$results = (array) $wpdb->get_results(
			"SELECT m.meta_value AS name, COUNT( * ) num_posts " .
			"FROM {$wpdb->posts} AS p " .
			"INNER JOIN {$wpdb->postmeta} AS m ON p.ID = m.post_id " .
			"WHERE p.post_type = 'site-review' AND m.meta_key = 'site_name' " .
			"GROUP BY name"
		);

		$counts = [];

		foreach( $results as $site ) {
			$counts[ $site->name ] = $site->num_posts;
		}

		wp_cache_set( $this->app->id, $counts, 'counts' );

		return isset( $counts[ $siteName ] ) ? $counts[ $siteName ] : 0;
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

			$post_id = $this->getReviewPostId( $review_id );

			if( !empty( $post_id ) ) {
				wp_delete_post( $post_id, true );
			}
		}
	}

	/**
	 * Get array of meta values for all of a post_type
	 *
	 * @param string $key
	 * @param string $status
	 * @param string $type
	 *
	 * @return array
	 */
	public function getMetaValues( $key = '', $status = 'publish', $type = 'site-review' )
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
			$type
		));
	}

	/**
	 * Gets an option from the plugin settings array using dot notation
	 *
	 * @param string $path
	 * @param mixed  $fallback
	 * @param string $suffix
	 *
	 * @return mixed
	 */
	public function getOption( $path = '', $fallback = '', $suffix = 'settings' )
	{
		$settings = get_option( "{$this->app->prefix}_{$suffix}", [] );

		$option = $this->getDotNotation( $settings, $path, $fallback );

		// fallback to setting defaults
		if( $suffix == 'settings' && empty( $option ) ) {
			$defaults = $this->app->defaults;

			if( isset( $defaults[ $path ] ) ) {
				$option = $defaults[ $path ];
			}
		}

		return $option;
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
			"WHERE p.post_type = 'site-review' " .
				"AND m1.meta_key = 'review_id' " .
				"AND m2.meta_key = 'site_name' " .
				"AND m2.meta_value = '%s'",
			$siteName
		));

		return array_keys( array_flip( $ids ) );
	}

	/**
	 * Get the review post ID from the review_id meta value
	 *
	 * @param string $review_id
	 *
	 * @return int|null
	 */
	public function getReviewPostId( $review_id )
	{
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare(
			"SELECT p.ID " .
			"FROM {$wpdb->posts} AS p " .
			"INNER JOIN {$wpdb->postmeta} AS m1 ON p.ID = m1.post_id " .
			"WHERE p.post_type = 'site-review' " .
				"AND m1.meta_key = 'review_id' " .
				"AND m1.meta_value = '%s'",
			$review_id
		));
	}

	/**
	 * Gets an array of saved Reviews
	 *
	 * @return array
	 */
	public function getReviews( array $args = [] )
	{
		$defaults = [
			'max_reviews'  => '10',
			'min_rating'   => '5',
			'order_by'     => 'date',
			'site_name'    => '',
		];

		$args = shortcode_atts( $defaults, $args );

		extract( $args );

		if( !empty( $site_name ) ) {
			$meta_query[] = [
				'key'   => 'site_name',
				'value' => $site_name,
			];
		}

		$meta_query[] = [
			'key'     => 'rating',
			'value'   => $min_rating,
			'compare' => '>=',
		];

		return get_posts([
			'meta_key'       => 'pinned',
			'order'          => 'DESC',
			'orderby'        => "meta_value $order_by",
			'post_status'    => 'publish',
			'post_type'      => 'site-review',
			'posts_per_page' => $max_reviews ? $max_reviews : -1,
			'meta_query'     => $meta_query,
		]);
	}

	/**
	 * Gets an selector option from the plugin settings array using dot notation
	 *
	 * @param string $path
	 * @param mixed  $fallback
	 *
	 * @return mixed
	 */
	public function getSelectorOption( $path = '', $fallback = '' )
	{
		$settings = $this->setSettings( true, false );

		return $this->getDotNotation( $settings, $path, $fallback );
	}

	/**
	 * Save a review to the database
	 *
	 * @param string $review_id
	 * @param bool   $update
	 *
	 * @return void
	 */
	public function postReview( $review_id, array $meta, $update = false )
	{
		$post_id = $this->getReviewPostId( $review_id );

		if( !empty( $post_id ) && !$update ) {
			return $post_id;
		}

		$post_data = [
			'comment_status' => 'closed',
			'ID'             => $post_id ? $post_id : 0,
			'ping_status'    => 'closed',
			'post_content'   => $meta['content'],
			'post_date'      => $meta['date'],
			'post_name'      => $meta['site_name'] . str_replace( ['review_', '_'], '-', $review_id ),
			'post_status'    => 'publish',
			'post_title'     => wp_strip_all_tags( $meta['title'] ),
			'post_type'      => 'site-review',
		];

		if( $this->getOption( 'general.require.approval', false ) ) {
			$post_data['post_status'] = 'pending';
		}

		$post_id = wp_insert_post( $post_data, true );

		if( is_wp_error( $post_id ) ) {
			$this->log->error( sprintf( '%s (%s)', $post_id->get_error_message(), $review_id ) );

			return false;
		}

		// add post_meta
		foreach( $meta as $field => $value ) {
			update_post_meta( $post_id, $field, $value );
		}

		return $post_id;
	}

	/**
	 * Resets an option to the provided value and returns the old value
	 *
	 * @param mixed  $value
	 * @param string $path
	 * @param string $suffix
	 *
	 * @return mixed
	 */
	public function resetOption( $value, $path = '', $suffix = 'settings' )
	{
		$option = $this->getOption( $path, '', $suffix );

		$this->setOption( $value, $path, $suffix );

		return $option;
	}

	/**
	 * Reverts a review title, date, and content to the originally submitted values
	 *
	 * @param string $postId
	 *
	 * @return int|false
	 */
	public function revertReview( $postId )
	{
		$post = get_post( $postId );

		if( !isset( $post->post_type ) || $post->post_type != 'site-review' ) {
			return false;
		}

		delete_post_meta( $post->ID, '_edit_last' );

		return wp_update_post([
			'ID'                => $post->ID,
			'post_content'      => get_post_meta( $post->ID, 'content', true ),
			'post_date'         => get_post_meta( $post->ID, 'date', true ),
			'post_title'        => get_post_meta( $post->ID, 'title', true ),
		]);
	}

	/**
	 * Sets an option to the plugin settings array using dot notation
	 *
	 * @param mixed  $value
	 * @param string $path
	 * @param string $suffix
	 *
	 * @return bool
	 */
	public function setOption( $value, $path = '', $suffix = 'settings' )
	{
		$option = get_option( "{$this->app->prefix}_{$suffix}", [] );

		$option = $this->setDotNotation( $path, $value, $option );

		return update_option( "{$this->app->prefix}_{$suffix}", $option );
	}

	/**
	 * Sets the default settings
	 *
	 * @param bool $mergeExistingSettings
	 * @param bool $updateSettings
	 *
	 * @return array
	 */
	public function setSettings( $mergeExistingSettings = true, $updateSettings = true )
	{
		$currentSettings = $mergeExistingSettings
			? get_option( "{$this->app->prefix}_settings", [] )
			: [];

		$currentSettings = $this->removeEmptyValuesFrom( $currentSettings );
		$defaultSettings = [];

		foreach( $this->app->defaults as $path => $value ) {
			// Don't save the default selector values as they are used anyway by default.
			if( !!$updateSettings && strpos( $path, '.selectors.' ) !== false ) {
				$value = '';
			}

			$defaultSettings = $this->setDotNotation( $path, $value, $defaultSettings );
		}

		$settings = array_replace_recursive( $defaultSettings, $currentSettings );

		if( $updateSettings ) {
			update_option( "{$this->app->prefix}_settings", $settings );
		}

		return $settings;
	}

	/**
	 * Gets a value from an array using a dot-notation path
	 *
	 * @param mixed  $settings
	 * @param string $path
	 * @param mixed  $fallback
	 *
	 * @return mixed
	 */
	protected function getDotNotation( $settings, $path, $fallback )
	{
		if( empty( $path ) ) {
			return $settings;
		}

		$keys = explode( '.', $path );

		foreach( $keys as $key ) {
			if( !isset( $settings[ $key ] ) ) {
				return $fallback;
			}
			$settings = $settings[ $key ];
		}

		return $settings;
	}

	/**
	 * Sets a value to an array using a dot-notation path
	 *
	 * @param string $path
	 * @param mixed  $value
	 * @param mixed  $option
	 *
	 * @return array
	 */
	protected function setDotNotation( $path, $value, $option )
	{
		$token = strtok( $path, '.' );

		$ref = &$option;

		while( $token !== false ) {
			$ref = is_array( $ref ) ? $ref : [];
			$ref = &$ref[ $token ];
			$token = strtok( '.' );
		}

		$ref = $value;

		return $option;
	}

	/**
	 * Removes empty values from an array
	 *
	 * @return array
	 */
	protected function removeEmptyValuesFrom( array $array )
	{
		$result = [];

		foreach( $array as $key => $value ) {
			if( !$value )continue;
			$result[ $key ] = is_array( $value )
				? $this->removeEmptyValuesFrom( $value )
				: $value;
		}

		return $result;
	}
}
