<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use Exception;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodes as Command;
use ReflectionException;

class RegisterShortcodes
{
	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		foreach( $command->shortcodes as $key ) {
			try {
				$shortcode = glsr_resolve( $this->getClassName( $key ) );
				add_shortcode( $key, [ $shortcode, 'printShortcode'] );
			}
			catch( Exception $e ) {
				glsr_resolve( 'Log\Logger' )->error( sprintf( 'Error registering shortcode. Message: %s "(%s:%s)"',
					$e->getMessage(),
					$e->getFile(),
					$e->getLine()
				));
			}
		}
	}

	/**
	 * @param string $shortcode
	 *
	 * @return string
	 */
	protected function getClassName( $shortcode )
	{
		$className = implode( '', array_map( 'ucfirst', preg_split( '/[-_]/', $shortcode ) ) );
		$className = "GeminiLabs\SiteReviews\Shortcodes\\$className";

		return $className;
	}
}
