<?php

/**
 * = Reviews Form shortcode
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcode;

class SiteReviews extends Shortcode
{
	/**
	 * @return void
	 */
	public function printShortcode( $atts = [] )
	{
		$defaults = [
			'class'      => '',
			'count'      => 10,
			'display'    => 'title,excerpt,author,date,rating,url',
			'pagination' => false,
			'rating'     => 5,
			'title'      => '',
		];

		$args = shortcode_atts( $defaults, $atts );

		extract( $args );

		$display = array_map( 'trim', explode( ',', $display ) );

		$author = in_array( 'author', $display );
		$date   = in_array( 'date', $display );
		$link   = in_array( 'link', $display );
		$rating = in_array( 'rating', $display );

		$display = array_intersect( ['title', 'excerpt'], $display );
		$display = $display
			? ( count($display) > 1 ? 'both' : array_shift( $display ) )
			: 'both';

		ob_start();

		echo '<div class="shortcode-site-reviews">';

		if( !empty( $title ) ) {
			printf( '<h2>%s</h2>', $title );
		}

		$this->html->renderPartial( 'reviews', [
			'class'       => $class,
			'display'     => $display,
			'max_reviews' => $count,
			'min_rating'  => $rating,
			'order_by'    => 'date',
			'pagination'  => $pagination,
			'show_author' => $author,
			'show_date'   => $date,
			'show_link'   => $link,
			'show_rating' => $rating,
			'site_name'   => 'local',
		]);

		echo '</div>';

		return ob_get_clean();
	}
}
