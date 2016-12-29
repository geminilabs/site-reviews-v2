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
use GeminiLabs\SiteReviews\Commands\RegisterWidgets as Command;

class RegisterWidgets
{
	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		global $wp_widget_factory;

		foreach( $command->widgets as $key => $values ) {

			$widgetClass = $this->getClassName( $key );

			try {
				// bypass register_widget() in order to pass our custom values to the widget
				$widget = new $widgetClass( glsr_app()->id . $key, $values['title'], $values );
				$wp_widget_factory->widgets[ $widgetClass ] = $widget;
			}
			catch( Exception $e ) {
				glsr_resolve( 'Log\Logger' )->error( sprintf( 'Error registering widget. Message: %s "(%s:%s)"',
					$e->getMessage(),
					$e->getFile(),
					$e->getLine()
				));
			}
		}
	}

	/**
	 * @param string $widget
	 *
	 * @return string
	 */
	protected function getClassName( $widget )
	{
		$className = implode( '', array_map( 'ucfirst', explode( '_', $widget ) ) );
		$className = "GeminiLabs\SiteReviews\Widgets\\$className";

		return $className;
	}
}
