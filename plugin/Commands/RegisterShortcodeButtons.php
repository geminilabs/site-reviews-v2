<?php

/**
 * =
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.3.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Commands;

class RegisterShortcodeButtons
{
	public $shortcodes;

	public function __construct( $input )
	{
		$this->shortcodes = $input;
	}
}
