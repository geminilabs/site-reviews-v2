<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Database;
use Vectorface\Whip\Whip;

class Helper
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Database
	 */
	protected $db;

	public function __construct( App $app, Database $db )
	{
		$this->app = $app;
		$this->db  = $db;
	}

	/**
	 * @param string $name
	 * @param string $path
	 *
	 * @return string
	 */
	public function buildClassName( $name, $path = '' )
	{
		$className = array_map( 'ucfirst', array_map( 'strtolower', preg_split( '/[-_]/', $name )));
		$className = implode( '', $className );

		return !empty( $path )
			? str_replace( '\\\\', '\\', sprintf( '%s\%s', $path, $className ))
			: $className;
	}

	/**
	 * @param string $name
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function buildMethodName( $name, $prefix = 'get' )
	{
		return $prefix . $this->buildClassName( $name );
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function get( $name )
	{
		$method = $this->buildMethodName( $name );

		if( !method_exists( $this, $method ))return;

		return call_user_func_array([ $this, $method ], array_slice( func_get_args(), 1 ));
	}

	/**
	 * @return null|string
	 */
	public function getIpAddress()
	{
		$cloudflareIPv4 = array_filter( explode( PHP_EOL, wp_remote_retrieve_body( 'https://www.cloudflare.com/ips-v4' )));
		$cloudflareIPv6 = array_filter( explode( PHP_EOL, wp_remote_retrieve_body( 'https://www.cloudflare.com/ips-v6' )));

		$ipAddress = ( new Whip( Whip::CLOUDFLARE_HEADERS | Whip::REMOTE_ADDR, [
			Whip::CLOUDFLARE_HEADERS => [
				Whip::IPV4 => $cloudflareIPv4,
				Whip::IPV6 => $cloudflareIPv6,
			],
		]))->getValidIpAddress();

		return $ipAddress ? $ipAddress : null;
	}

	/**
	 * @param string $optionPath
	 * @param mixed  $fallback
	 *
	 * @return mixed
	 */
	protected function getOption( $optionPath, $fallback )
	{
		return !empty( $optionPath )
			? $this->db->getOption( $optionPath, $fallback, 'settings' )
			: '';
	}

	/**
	 * @return array
	 */
	protected function getOptions()
	{
		return $this->db->getOptions( 'settings' );
	}

	/**
	 * @param int $postId
	 *
	 * @return null|object
	 */
	protected function getReview( $postId )
	{
		return $this->db->getReview( get_post( $postId ));
	}

	/**
	 * @return array
	 */
	protected function getReviews( array $args = [] )
	{
		return $this->db->getReviews( $args )->reviews;
	}
}
