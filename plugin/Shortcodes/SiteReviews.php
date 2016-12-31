<?php

/**
 * = Site Reviews shortcode
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcode;
use GeminiLabs\SiteReviews\Traits\SiteReviews as Common;

class SiteReviews extends Shortcode
{
	use Common;

	/**
	 * @return string
	 */
	public function printShortcode( $atts = [] )
	{
		$defaults = [
			'class'      => '',
			'count'      => 10,
			'display'    => 'all',
			'hide'       => '',
			'pagination' => false,
			'rating'     => 5,
			'title'      => '',
		];

		$args = shortcode_atts( $defaults, $atts );

		ob_start();

		echo '<div class="shortcode-site-reviews">';

		if( !empty( $args['title'] ) ) {
			printf( '<h3 class="glsr-shortcode-title">%s</h3>', $args['title'] );
		}

		$this->renderReviews( $args );

		echo '</div>';

		return ob_get_clean();
	}
}
