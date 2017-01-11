<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Fields;

use GeminiLabs\SiteReviews\Html\Fields\Textarea;

class Code extends Textarea
{
	/**
	 * @return string
	 */
	public function render()
	{
		return parent::render([
			'class' => 'large-text code',
		]);
	}
}
