<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
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

		$star = is_admin() ? ' star star%s' : '%s';
		$star = sprintf( '<span class="glsr-star%s"></span>', $star );

		$class = 'glsr-review-rating' . ( is_admin() ? ' star-rating' : '' );

		for( $i = 0; $i < $stars; $i++ ) {
			$rating .= sprintf( $star, '-full' );
		}

		for( $i = 5; $i > $stars; $i-- ) {
			$rating .= sprintf( $star, '-empty' );
		}

		return sprintf( '<span class="%s">%s</span>', $class, $rating );
	}
}
