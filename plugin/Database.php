<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Database\Posts;
use GeminiLabs\SiteReviews\Database\Reviews;
use WP_Query;

class Database
{
	use Posts;
	use Reviews;

	/**
	 * @var App
	 */
	protected $app;

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * Get the current page number for the global query
	 *
	 * @return int
	 */
	public function getCurrentPageNumber()
	{
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : (
			get_query_var( 'page' ) ? get_query_var( 'page' ) : 1
		);

		return intval( $paged );
	}

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
		$settings = get_option( "{$this->app->prefix}_{$suffix}", [] );

		$option = $this->getDotNotationValue( $settings, $path, $fallback );

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
	 * Gets an selector option from the plugin settings array using dot notation
	 *
	 * @param string $path
	 * @param mixed  $fallback
	 *
	 * @return mixed
	 */
	public function getSelectorOption( $path = '', $fallback = '' )
	{
		$settings = $this->setDefaultSettings(['update' => false ]);

		return $this->getDotNotationValue( $settings, $path, $fallback );
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

		$option = $this->convertDotNotationPath( $path, $value, $option );

		return update_option( "{$this->app->prefix}_{$suffix}", $option );
	}

	/**
	 * Sets the default settings
	 *
	 * @return array
	 */
	public function setDefaultSettings( array $args = [] )
	{
		$defaults = [
			'data'   => null,
			'merge'  => true,
			'update' => true,
		];

		$args = shortcode_atts( $defaults, $args );

		$currentSettings = $args['merge']
			? get_option( "{$this->app->prefix}_settings", [] )
			: [];

		$currentSettings = $this->removeEmptyValuesFrom( $currentSettings );
		$defaultSettings = [];

		$args['data'] ?: $args['data'] = $this->app->getDefaults();

		foreach( $args['data'] as $path => $value ) {
			// Don't save the default selector values as they are used anyway by default.
			if( !!$args['update'] && strpos( $path, '.selectors.' ) !== false ) {
				$value = '';
			}

			$defaultSettings = $this->convertDotNotationPath( $path, $value, $defaultSettings );
		}

		$settings = array_replace_recursive( $defaultSettings, $currentSettings );

		if( $args['update'] ) {
			update_option( "{$this->app->prefix}_settings", $settings );
		}

		return $settings;
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
	protected function getDotNotationValue( $value, $path, $fallback )
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
	 * Convert a dot-notation path to an array
	 *
	 * @param string $path
	 * @param mixed  $value
	 * @param mixed  $option
	 *
	 * @return array
	 */
	protected function convertDotNotationPath( $path, $value, $option )
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
	 * Removes empty values from an array
	 *
	 * @return array
	 */
	protected function removeEmptyValuesFrom( array $array )
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
