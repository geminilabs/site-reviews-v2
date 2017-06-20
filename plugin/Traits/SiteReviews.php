<?php

/**
 * Shared shortcode/widget methods
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Traits;

trait SiteReviews
{
	/**
	 * @return
	 */
	public function renderReviews( array $args )
	{
		if( !is_array( $args['hide'] )) {
			$args['hide'] = array_map( 'trim', array_filter( explode( ',', $args['hide'] )));
		}

		foreach( $args['hide'] as $key ) {
			$args[ 'hide_' . $key ] = true;
		}

		// "type" takes precedence over "display"
		if( empty( $args['type'] )) {
			$args['type'] = $args['display'];
		}

		echo glsr_resolve( 'Html' )->renderPartial( 'reviews', $args );
	}
}
