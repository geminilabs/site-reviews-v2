<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Commands;

class TogglePinned
{
	public $id;
	public $pinned;

	public function __construct( $input )
	{
		isset( $input['pinned'] ) ?: $input['pinned'] = false;

		$this->id     = $input['id'];
		$this->pinned = wp_validate_boolean( $input['pinned'] );
	}
}
