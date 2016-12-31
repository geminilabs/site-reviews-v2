<?php

/**
 * =
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
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
		if( !is_array( $args['hide'] ) ) {
			$args['hide'] = array_map( 'trim', array_filter( explode( ',', $args['hide'] ) ) );
		}

		foreach( $args['hide'] as $key ) {
			$args[ 'hide_' . $key ] = true;
		}

		$args['site_name'] = $args['display'];

		glsr_resolve( 'Html' )->renderPartial( 'reviews', $args );
	}
}
