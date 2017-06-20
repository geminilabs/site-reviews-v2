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

class StarRating extends Base
{
	/**
	 * @return string
	 */
	public function render()
	{
		$defaults = [
			'hidden' => false,
			'rating' => 5,
			'schema' => false,
		];
		$this->args = shortcode_atts( $defaults, $this->args );
		$attributes = '';
		if( is_admin() || !wp_validate_boolean( $this->args['hidden'] )) {
			$class = is_admin() ? ' star-rating' : '';
			$attributes .= sprintf( ' class="glsr-review-rating%s"', $class );
		}
		if( !is_admin() && wp_validate_boolean( $this->args['schema'] )) {
			$attributes .= ' itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"';
		}
		return sprintf( '<span%s>%s</span>', $attributes, $this->buildStars() );
	}

	/**
	 * @param int $maxStars
	 * @param int $minStars
	 * @return string
	 */
	protected function buildStars( $maxStars = 5, $minStars = 1 )
	{
		$rating = '';

		if( is_admin() || !wp_validate_boolean( $this->args['hidden'] )) {
			$star = is_admin() ? ' star star%s' : '%s';
			$star = sprintf( '<span class="glsr-star%s"></span>', $star );

			$roundedRating = floor( round( $this->args['rating'], 1 ) * 2 ) / 2;

			for( $i = 0; $i < $maxStars; $i++ ) {
				if( $roundedRating == ( $i + 0.5 )) {
					$rating .= sprintf( $star, '-half' );
				}
				else if( $roundedRating > $i ) {
					$rating .= sprintf( $star, '-full' );
				}
				else {
					$rating .= sprintf( $star, '-empty' );
				}
			}
		}
		if( !is_admin() && wp_validate_boolean( $this->args['schema'] )) {
			$rating .= sprintf( '<meta itemprop="ratingValue" content="%s">', $this->args['rating'] );
			$rating .= sprintf( '<meta itemprop="bestRating" content="%s">', $maxStars );
			$rating .= sprintf( '<meta itemprop="worstRating" content="%s">', $minStars );
		}
		return $rating;
	}
}
