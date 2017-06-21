<?php

/**
 * Site Reviews Form shortcode
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcode;
use GeminiLabs\SiteReviews\Traits\SiteReviewsForm as Common;

class SiteReviewsForm extends Shortcode
{
	use Common;

	/**
	 * @var bool|string
	 */
	public $id = false;

	/**
	 * @return null|string
	 */
	public function printShortcode( $atts = [] )
	{
		$defaults = [
			'assign_to'   => '',
			'category'    => '',
			'class'       => '',
			'description' => '',
			'hide'        => '',
			'title'       => '',
		];

		$atts = shortcode_atts( $defaults, $atts );

		$atts = $this->makeCompatible( $atts );

		if( $atts['assign_to'] == 'post_id' ) {
			$atts['assign_to'] = intval( get_the_ID() );
		}

		$atts['hide'] = explode( ',', $atts['hide'] );
		$atts['hide'] = array_filter( $atts['hide'], function( $value ) {
			return in_array( $value, [
				'email',
				'name',
				'terms',
				'title',
			]);
		});

		ob_start();

		echo '<div class="shortcode-reviews-form">';

		if( !empty( $atts['title'] )) {
			printf( '<h3 class="glsr-shortcode-title">%s</h3>', $atts['title'] );
		}

		if( !$this->renderRequireLogin() ) {
			echo $this->renderForm( $atts );
		}

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
		$args['hide'] = str_replace( 'reviewer', 'name', $args['hide'] );

		$hide = explode( ',', $args['hide'] );

		$args['hide'] = implode( ',', array_unique( array_map( 'trim', $hide )) );

		return $args;
	}
}
