<?php

/**
 * Site Reviews shortcode
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
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
			'assigned_to' => '',
			'category'    => '',
			'class'       => '',
			'count'       => 10,
			'display'     => 'all',
			'hide'        => '',
			'pagination'  => false,
			'rating'      => 1,
			'schema'      => false,
			'title'       => '',
			'type'        => '',
		];

		$args = shortcode_atts( $defaults, $atts );

		$args = $this->makeCompatible( $args );

		if( $args['assigned_to'] == 'post_id' ) {
			$args['assigned_to'] = intval( get_the_ID() );
		}

		ob_start();

		echo '<div class="shortcode-site-reviews">';

		if( !empty( $args['title'] )) {
			printf( '<h3 class="glsr-shortcode-title">%s</h3>', $args['title'] );
		}

		$this->renderReviews( $args );

		echo '</div>';

		return ob_get_clean();
	}

	/**
	 * Maintain backwards compatibility with version <= v1.2.1
	 *
	 * @return array
	 */
	protected function makeCompatible( array $args )
	{
		$hide    = ['author','date','excerpt','rating','response','title'];
		$display = array_map( 'trim', explode( ',', $args['display'] ));

		if( count( array_intersect( $hide, $display )) > 0 ) {
			$args['hide']    = array_diff( $hide, $display );
			$args['display'] = '';
		}

		return $args;
	}
}
