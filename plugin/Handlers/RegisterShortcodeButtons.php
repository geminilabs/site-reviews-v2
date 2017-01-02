<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv2 or later
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use Exception;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodeButtons as Command;
use ReflectionException;

class RegisterShortcodeButtons
{
	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		$properties = [];

		foreach( $command->shortcodes as $slug => $args ) {

			$shortcode = glsr_resolve( $this->getClassName( $slug ) )->register( $slug, $args );

			$properties[ $slug ] = $shortcode->properties;
		}

		glsr_app()->mceShortcodes = $properties;
	}

	/**
	 * @param string $shortcode
	 *
	 * @return string
	 */
	protected function getClassName( $shortcode )
	{
		$className = implode( '', array_map( 'ucfirst', preg_split( '/[-_]/', $shortcode ) ) );
		$className = "GeminiLabs\SiteReviews\Shortcodes\Buttons\\$className";

		return $className;
	}
}
