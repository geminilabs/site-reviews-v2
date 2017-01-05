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

trait Posts
{
	protected $app;

	public function getPosts( array $args = [] )
	{}

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

		return get_terms( $taxonomy, wp_parse_args( $args, [
			'fields'     => 'id=>name',
			'hide_empty' => false,
		]));
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
