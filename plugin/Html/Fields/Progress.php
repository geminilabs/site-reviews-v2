<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Fields;

use GeminiLabs\SiteReviews\Html\Fields\Base;

class Progress extends Base
{
	protected $value;

	public function __construct( array $args = [] )
	{
		$value = intval( $args['value'] );

		$this->value = ( 0 <= $value && $value <= 100 ) ? $value : 0; // 0-100

		$args['name']  = '';
		$args['value'] = '';

		return parent::__construct( $args );
	}

	/**
	 * @return string
	 */
	public function render()
	{
		$defaults = [
			'data-active-text'   => __( 'Please wait...', 'geminilabs-site-reviews' ),
			'data-inactive-text' => __( 'Inactive', 'geminilabs-site-reviews' ),
		];

		$args       = $this->mergeAttributesWith( $defaults );
		$attributes = $this->implodeAttributes( $defaults );

		preg_match( '/\bactive(?=$|\s)/', $args['class'], $matches );

		$initialText = isset( $matches[0] )
			? $args['data-active-text']
			: $args['data-inactive-text'];

		$progressBar = '' .
		'<div class="glsr-progress %5$s" data-inactive-text="%2$s" data-active-text="%3$s">' .
			'<div class="glsr-progress-bar" style="width: %4$d%%;"><span>%1$s</span></div>' .
			'<div class="glsr-progress-background"><span>%1$s</span></div>' .
		'</div>';

		return sprintf( $progressBar,
			$initialText,
			$args['data-inactive-text'],
			$args['data-active-text'],
			$this->value,
			$args['class'],
			$this->generateDescription()
		);
	}
}
