<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Partials;

use GeminiLabs\SiteReviews\App;

abstract class Base
{
	/**
	 * @var App
	 */
	public $app;

	/**
	 * @var array
	 */
	public $args = [];

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * Generate select option
	 *
	 * @param string $value
	 * @param string $title
	 * @param string $selected
	 *
	 * @return string
	 */
	protected function selectOption( $value, $title, $selected )
	{
		return sprintf( '<option value="%s" %s>%s</option>',
			$value,
			selected( $selected, $value, false ),
			$title
		);
	}
}
