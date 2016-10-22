<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Partials;

use GeminiLabs\SiteReviews\Html\Partials\Base;

class Rating extends Base
{
	/**
	 * Generate a star rating
	 *
	 * @return string
	 */
	public function render()
	{
		$defaults = [
			'stars' => 5,
		];

		$args = shortcode_atts( $defaults, $this->args );

		extract( $args );

		$rating = '';
		$star   = '<span class="glsr-star star star-%s"></span>';

		for( $i = 0; $i < $stars; $i++ ) {
			$rating .= sprintf( $star, 'full' );
		}

		for( $i = 5; $i > $stars; $i-- ) {
			$rating .= sprintf( $star, 'empty' );
		}

		return sprintf( '<span class="glsr-review-rating star-rating">%s</span>', $rating );
	}
}
