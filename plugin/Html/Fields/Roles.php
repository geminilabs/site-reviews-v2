<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Fields;

use GeminiLabs\SiteReviews\Html\Fields\Select;

class Roles extends Select
{
	/**
	 * @return string
	 */
	public function render()
	{
		return parent::render([
			'options' => wp_roles()->get_names(),
		]);
	}
}
