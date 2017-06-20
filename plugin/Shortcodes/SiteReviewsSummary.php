<?php

/**
 * Site Reviews Sumary shortcode
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     2.3.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcode;
use GeminiLabs\SiteReviews\Rating;

class SiteReviewsSummary extends Shortcode
{
	/**
	 * @return string
	 */
	public function printShortcode( $atts = [] )
	{
		$this->normalize( $atts );
		$this->rating = $this->app->make( 'Rating' );
		$reviews = $this->db->getReviews( $this->args );
		$ratingAverage = $this->rating->getAverage( $reviews->reviews );
		ob_start();
		echo '<div class="shortcode-site-reviews-summary">';
		if( !empty( $this->args['title'] )) {
			printf( '<h3 class="glsr-shortcode-title">%s</h3>', $this->args['title'] );
		}
		echo $this->buildSummary( $ratingAverage, count( $reviews->reviews ));
		echo $this->buildPercentBars( $reviews->reviews );
		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * @param string $label
	 * @param string $percentage
	 * @param string $count
	 * @return string
	 */
	protected function buildPercentBar( $label, $percentage, $count )
	{
		return sprintf(
			'<div class="glsr-bar">' .
				'<span class="glsr-bar-label">%1$s</span>' .
				'<span class="glsr-bar-background"><span class="glsr-bar-percent" style="width:%2$s;"></span></span>' .
				'<span class="glsr-bar-count">%2$s</span>' .
			'</div>',
			$label,
			$percentage
		);
	}

	/**
	 * @param int $maxRating
	 * @return string
	 */
	protected function buildPercentBars( array $reviews, $maxRating = 5 )
	{
		$bars = '';
		$ratingLabels = $this->args['labels'];
		$ratingPercentages = preg_filter( '/$/', '%', $this->rating->getPercentages( $reviews ));
		$ratingCounts = $this->rating->getCounts( $reviews );
		for( $i = $maxRating; $i > 0; $i-- ) {
			$bars .= $this->buildPercentBar( $ratingLabels[$i], $ratingPercentages[$i], $ratingCounts[$i] );
		}
		return sprintf( '<div class="glsr-percentage-bars">%s</div>', $bars );
	}

	/**
	 * @param float $rating
	 * @return string
	 */
	protected function buildRating( $rating )
	{
		return $this->app->make( 'Html' )->renderPartial( 'star-rating', [
			'rating' => $rating,
		]);
	}

	/**
	 * @param float $rating
	 * @param int $count
	 * @return string
	 */
	protected function buildSummary( $rating, $count )
	{
		return sprintf( '<div class="glsr-summary"><span class="glsr-summary-rating">%s</span>%s</div>',
			$rating,
			$this->buildRating( $rating ) . $this->buildSummaryText( $rating, $count )
		);
	}

	/**
	 * @param float $rating
	 * @param int $count
	 * @return string
	 */
	protected function buildSummaryText( $rating, $count )
	{
		$summary = str_replace(
			['{rating}','{max}','{num}'],
			[$rating, Rating::MAX_RATING, $count],
			$this->args['summary']
		);
		return sprintf( '<span class="glsr-summary-text">%s</span>', $summary );
	}

	/**
	 * @return void
	 */
	protected function normalize( $atts )
	{
		$defaults = [
			'assigned_to' => '',
			'category'    => '',
			'class'       => '',
			'count'       => -1,
			'labels'      => '',
			'rating'      => 1,
			'summary'     => __( '{rating} out of {max} stars (based on {num} reviews)', 'site-reviews' ),
			'title'       => '',
			'type'        => '',
		];
		$this->args = shortcode_atts( $defaults, $atts );
		$defaultLabels = [
			__( 'Excellent', 'site-reviews' ),
			__( 'Very good', 'site-reviews' ),
			__( 'Average', 'site-reviews' ),
			__( 'Poor', 'site-reviews' ),
			__( 'Terrible', 'site-reviews' ),
		];
		$labels = wp_parse_args(
			array_filter( explode( ',', $this->args['labels'] )),
			$defaultLabels
		);
		$this->args['labels'] = array_combine( [5,4,3,2,1], array_slice( $labels, 0, 5 ));
	}
}
