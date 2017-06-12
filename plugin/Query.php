<?php

/**
 * This class interacts with the global WP_Query and/or builds SQL/WP_Query strings
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;

class Query
{
	/**
	 * @var App
	 */
	protected $app;

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * Build a WP_Query meta_query
	 *
	 * @return array
	 */
	public function buildMeta( array $meta = [] )
	{
		$metaQuery = [];

		foreach( $meta as $key => $query ) {
			if( $key == 'assigned_to' && !empty( $query['value'] )) {
				$metaQuery[] = $query;
			}
			if( $key == 'type'
				&& !in_array( $query['value'], ['','all'] )) {
				$metaQuery[] = $query;
			}
			if( $key == 'rating'
				&& is_numeric( $query['value'] )
				&& in_array((int) $query['value'], range(2,5))) {
				$metaQuery[] = $query;
			}
		}

		return $metaQuery;
	}

	/**
	 * Build a SQL 'OR' string from an array
	 *
	 * $values can either be an array or a comma-separated string
	 *
	 * @param string|array $values
	 *
	 * @return string
	 */
	public function buildSqlOr( $values, $sprintfString )
	{
		is_array( $values ) ?: $values = explode( ',', $values );

		$values = array_filter( array_map( 'trim', $values ));

		$values = array_map( function( $value ) use( $sprintfString ) {
			return sprintf( $sprintfString, $value );
		}, $values );

		return implode( ' OR ', $values );
	}

	/**
	 * Build a WP_Query tax_query from a term ID array
	 *
	 * @return array
	 */
	public function buildTerms( array $terms = [] )
	{
		$query = [];

		if( !empty( $terms )) {
			$query[] = [
				'taxonomy' => App::TAXONOMY,
				'field'    => 'term_id',
				'terms'    => $terms,
			];
		}

		return $query;
	}

	/**
	 * Get the current page number from the global query
	 *
	 * @return int
	 */
	public function getPaged()
	{
		$paged = intval( get_query_var(( is_front_page() ? 'page' : 'paged' )));

		return $paged ?: 1;
	}
}
