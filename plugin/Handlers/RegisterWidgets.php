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
use GeminiLabs\SiteReviews\Commands\RegisterWidgets as Command;

class RegisterWidgets
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
		global $wp_widget_factory;

		foreach( $command->widgets as $key => $values ) {

			$widgetClass = implode( '', array_map( 'ucfirst', explode( '_', $key ) ) );
			$widgetClass = "GeminiLabs\SiteReviews\Widgets\\$widgetClass";

			try {
				// bypass register_widget() in order to pass our custom values to the widget
				$widget = new $widgetClass( "{$this->app->id}_{$key}", $values['title'], $values );
				$wp_widget_factory->widgets[ $widgetClass ] = $widget;
			}
			catch( Exception $e ) {
				$this->app->make( 'Log\Logger' )->error( sprintf( 'Error registering widget. Message: %s "(%s:%s)"',
					$e->getMessage(),
					$e->getFile(),
					$e->getLine()
				));
			}
		}
	}
}
