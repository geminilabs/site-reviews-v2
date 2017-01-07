<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html;

use GeminiLabs\SiteReviews\App;
use ReflectionException;

class Partial
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $args;

	public function __construct( App $app )
	{
		$this->app  = $app;
		$this->args = [];
	}

	/**
	 * Normalize the partial arguments
	 *
	 * @return $this
	 */
	public function normalize( $name, array $args = [] )
	{
		$defaults = [
			'partial' => $name,
		];

		$this->args = wp_parse_args( $args, $defaults );

		return $this;
	}

	/**
	 * Render the partial
	 *
	 * @param mixed $print
	 *
	 * @return string|void
	 */
	public function render( $print = true )
	{
		$className = sprintf( 'GeminiLabs\SiteReviews\Html\Partials\%s', ucfirst( $this->args['partial'] ) );

		$instance = $this->app->make( $className );

		$instance->args = $this->args;

		$rendered = $instance->render();

		$rendered = apply_filters( 'site-reviews/rendered/partial', $rendered, $this->args['partial'] );

		if( !!$print && $print !== 'return' ) {
			echo $rendered;
		}

		return $rendered;
	}
}
