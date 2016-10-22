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
use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodes as Command;
use ReflectionException;

class RegisterShortcodes
{
	/**
	 * @var App
	 */
	protected $app;

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * return void
	 */
	public function handle( Command $command )
	{
		foreach( $command->shortcodes as $key ) {

			$shortcodeClass = implode( '', array_map( 'ucfirst', explode( '_', $key ) ) );
			$shortcodeClass = "GeminiLabs\SiteReviews\Shortcodes\\$shortcodeClass";

			try {
				$shortcode = $this->app->make( $shortcodeClass );
				add_shortcode( $key, [ $shortcode, 'printShortcode'] );
			}
			catch( Exception $e ) {
				$this->app->make( 'Log\Logger' )->error( sprintf( 'Error registering shortcode. Message: %s "(%s:%s)"',
					$e->getMessage(),
					$e->getFile(),
					$e->getLine()
				));
			}
		}
	}
}
