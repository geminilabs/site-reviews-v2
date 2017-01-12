<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Fields;

use GeminiLabs\SiteReviews\Html\Fields\Text;

class Submit extends Text
{
	/**
	 * @return string
	 */
	public function render()
	{
		if( isset( $this->args['name'] )) {
			$this->args['name'] = 'submit';
		}

		return parent::render([
			'class'  => 'button button-primary',
			'type'   => 'submit',
		]);
	}
}
