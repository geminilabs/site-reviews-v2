<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use Exception;
use GeminiLabs\SiteReviews\Providers\ProviderInterface;
use ReflectionClass;

abstract class Container
{
	/**
	 * The current globally available container (if any).
	 *
	 * @var static
	 */
	protected static $instance;

	protected $services = [];

	/**
	 * Set/get the globally available instance of the container.
	 *
	 * @return static
	 */
	public static function load()
	{
		if( is_null( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * This is the Application entry point
	 *
	 * @return void
	 */
	abstract public function init();

	/**
	 *
	 * @param string $service
	 *
	 * @return mixed
	 */
	public function __get( $service )
	{
		if( !isset( $this->services[ $service ] ) ) {
			return null;
		}

		if( !is_callable( $this->services[ $service ] ) ) {
			return $this->services[ $service ];
		}

		return $this->services[ $service ]( $this );
	}

	/**
	 *
	 * @param string $service
	 * @param string $callback
	 *
	 * @return void
	 */
	public function __set( $service, $callback )
	{
		$this->services[ $service ] = $callback;
	}

   /**
	 * Register a Provider.
	 *
	 * @param ProviderInterface $provider
	 *
	 * @return void
	 */
	public function register( ProviderInterface $provider )
	{
		$provider->register( $this );
	}

	/**
	 * Bind a singleton instance to the container.
	 *
	 * @param string $alias
	 * @param string $binding
	 *
	 * @return void
	 */
	public function singleton( $alias, $binding )
	{
		$this->bind( $alias, $this->make( $binding ) );
	}

	/**
	 * Bind a listener to an event.
	 *
	 * @param string $tag
	 * @param string $listener
	 * @param int    $priority
	 * @param int    $acceptedArgs
	 *
	 * @return void
	 */
	public function addListener( $tag, $listener, $priority = 10, $acceptedArgs = 1 )
	{
		$app = $this;

		add_action( $tag, function( $event ) use ( $listener, $app ) {
			$app->make( $listener )->handle( $event );
		}, $priority, $acceptedArgs );
	}

	/**
	 * Bind a service to the container.
	 *
	 * @param string $alias
	 * @param string $concrete
	 *
	 * @return mixed
	 */
	public function bind( $alias, $concrete )
	{
		$this->services[ $alias ] = $concrete;
	}

	/**
	 * Request a service from the container.
	 *
	 * @param string      $alias
	 * @param bool|string $prefixed
	 *
	 * @return mixed
	 */
	public function make( $alias, $prefixed = false )
	{
		if( isset( $this->services[ $alias ] ) && is_callable( $this->services[ $alias ] ) ) {
			return call_user_func_array( $this->services[ $alias ], [ $this ] );
		}

		if( isset( $this->services[ $alias ] ) && is_object( $this->services[ $alias ] ) ) {
			return $this->services[ $alias ];
		}

		if( isset( $this->services[ $alias ] ) && class_exists( $this->services[ $alias ] ) ) {
			return $this->resolve( $this->services[ $alias ] );
		}

		// Allow unbound aliases that omit the root namespace
		// i.e. 'Html\Field' translates to 'GeminiLabs\SiteReviews\Html\Field'
		if( $prefixed === false && strpos( $alias, __NAMESPACE__ ) === false && !class_exists( $alias ) ) {
			return $this->make( __NAMESPACE__ . "\\$alias" , 'prefix alias with the namespace' );
		}

		return $this->resolve( $alias );
	}

	/**
	 * Resolve class dependencies automatically
	 *
	 * @param string $class
	 *
	 * @return mixed
	 */
	private function resolve( $class )
	{
		$reflector = new ReflectionClass( $class );

        // If the type is not instantiable (i.e. interface/abstract class) then bail.
		if( !$reflector->isInstantiable() ) {
			$message = "Target [$class] is not instantiable.";

			throw new Exception( $message );
		}

		$constructor = $reflector->getConstructor();

		// No constructor means no dependencies so we can just resolve the instance right away.
		if( is_null( $constructor ) ) {
			return new $class;
		}

		$parameters = $constructor->getParameters();

		$newInstanceParameters = [];

		foreach( $parameters as $parameter ) {

			// Allow array parameters if they have a default value
			if( $parameter->isArray() && $parameter->isDefaultValueAvailable() ) {
				$newInstanceParameters[] = $parameter->getDefaultValue();
				continue;
			}

			// This will throw a TypeError if parameter is not a class
			if( is_null( $parameter->getClass() ) ) {
				$newInstanceParameters[] = null;
				continue;
			}

			$newInstanceParameters[] = $this->make(
				$parameter->getClass()->getName()
			);
		}

		return $reflector->newInstanceArgs( $newInstanceParameters );
	}
}
