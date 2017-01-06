<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv2 or later
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Database;

interface Contract
{
	/**
	 * Gets an option from the plugin settings array using dot notation
	 *
	 * @param string $path
	 * @param mixed  $fallback
	 * @param string $suffix
	 *
	 * @return mixed
	 */
	public function getOption( $path = '', $fallback = '', $suffix = 'settings' );

	/**
	 * Resets an option to the provided value and returns the old value
	 *
	 * @param mixed  $value
	 * @param string $path
	 * @param string $suffix
	 *
	 * @return mixed
	 */
	public function resetOption( $value, $path = '', $suffix = 'settings' );

	/**
	 * Sets an option to the plugin settings array using dot notation
	 *
	 * @param mixed  $value
	 * @param string $path
	 * @param string $suffix
	 *
	 * @return bool
	 */
	public function setOption( $value, $path = '', $suffix = 'settings' );

	/**
	 * Convert a dot-notation path to an array
	 *
	 * @param string $path
	 * @param mixed  $value
	 * @param mixed  $option
	 *
	 * @return array
	 */
	public function convertPathToArray( $path, $value, $option );

	/**
	 * Gets a value from an array using a dot-notation path
	 *
	 * @param mixed  $value
	 * @param string $path
	 * @param mixed  $fallback
	 *
	 * @return mixed
	 */
	public function getValueFromPath( $value, $path, $fallback );

	/**
	 * Removes empty values from an array
	 *
	 * @return array
	 */
	public function removeEmptyValuesFrom( array $array );
}
