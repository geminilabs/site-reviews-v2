<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv2 or later
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Database;

trait Options
{
	protected $app;

	/**
	 * Gets an option from the plugin settings array using dot notation
	 *
	 * @param string $path
	 * @param mixed  $fallback
	 * @param string $suffix
	 *
	 * @return mixed
	 */
	public function getOption( $path = '', $fallback = '', $suffix = 'settings' )
	{
		$value = get_option( "{$this->app->prefix}_{$suffix}", [] );

		$option = $this->getValueFromPath( $value, $path, $fallback );

		// fallback to setting defaults
		if( $suffix == 'settings' && empty( $option ) ) {
			$defaults = $this->app->getDefaults();

			if( isset( $defaults[ $path ] ) ) {
				$option = $defaults[ $path ];
			}
		}

		return $option;
	}

	/**
	 * Resets an option to the provided value and returns the old value
	 *
	 * @param mixed  $value
	 * @param string $path
	 * @param string $suffix
	 *
	 * @return mixed
	 */
	public function resetOption( $value, $path = '', $suffix = 'settings' )
	{
		$option = $this->getOption( $path, '', $suffix );

		$this->setOption( $value, $path, $suffix );

		return $option;
	}

	/**
	 * Sets an option to the plugin settings array using dot notation
	 *
	 * @param mixed  $value
	 * @param string $path
	 * @param string $suffix
	 *
	 * @return bool
	 */
	public function setOption( $value, $path = '', $suffix = 'settings' )
	{
		$option = get_option( "{$this->app->prefix}_{$suffix}", [] );
		$option = $this->convertPathToArray( $path, $value, $option );

		return update_option( "{$this->app->prefix}_{$suffix}", $option );
	}

	/**
	 * Convert a dot-notation path to an array
	 *
	 * @param string $path
	 * @param mixed  $value
	 * @param mixed  $option
	 *
	 * @return array
	 */
	public function convertPathToArray( $path, $value, $option )
	{
		$token = strtok( $path, '.' );

		$ref = &$option;

		while( $token !== false ) {
			$ref = is_array( $ref ) ? $ref : [];
			$ref = &$ref[ $token ];
			$token = strtok( '.' );
		}

		$ref = $value;

		return $option;
	}

	/**
	 * Gets a value from an array using a dot-notation path
	 *
	 * @param mixed  $value
	 * @param string $path
	 * @param mixed  $fallback
	 *
	 * @return mixed
	 */
	public function getValueFromPath( $value, $path, $fallback )
	{
		if( empty( $path ) ) {
			return $value;
		}

		$keys = explode( '.', $path );

		foreach( $keys as $key ) {
			if( !isset( $value[ $key ] ) ) {
				return $fallback;
			}
			$value = $value[ $key ];
		}

		return $value;
	}

	/**
	 * Removes empty values from an array
	 *
	 * @return array
	 */
	public function removeEmptyValuesFrom( array $array )
	{
		$result = [];

		foreach( $array as $key => $value ) {
			if( !$value )continue;
			$result[ $key ] = is_array( $value )
				? $this->removeEmptyValuesFrom( $value )
				: $value;
		}

		return $result;
	}
}
