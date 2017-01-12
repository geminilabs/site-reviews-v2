<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 *
 * Much of the code in this class is derived from WP Session Manager (1.2.0)
 * Copyright (c) <Eric Mann>
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use PasswordHash;

class Session
{
	/**
	 * Unix timestamp when session expires.
	 *
	 * @var int
	 */
	protected $expiry;

	/**
	 * Unix timestamp indicating when the expiration time needs to be reset.
	 *
	 * @var int
	 */
	protected $expiryReset;

	/**
	 * @var string
	 */
	protected $prefix;

	/**
	 * @var App
	 */
	protected $app;

	/**
	 * The current session data.
	 *
	 * @var array
	 */
	protected $session;

	/**
	 * ID of the current session.
	 *
	 * @var string
	 */
	protected $sessionId;

	public function __construct( App $app )
	{
		$this->app = $app;

		$this->prefix = "_{$this->app->prefix}_session";

		$cookieId = filter_input( INPUT_COOKIE, $this->app->id );

		if( $cookieId ) {

			$cookie = explode( '||', stripslashes( $cookieId ));

			$this->sessionId   = $cookie[0];
			$this->expiry      = $cookie[1];
			$this->expiryReset = $cookie[2];

			// Update the session expiration if we're past the reset time
			if( time() > $this->expiryReset ) {
				$this->resetCookieExpiration();
				$this->resetSessionExpiration();
			}
		}
		else {
			$this->sessionId = $this->generateSessionId();
			$this->resetCookieExpiration();
		}

		$this->getSession();
		$this->setCookie();

		add_action( 'site-reviews/schedule/session/purge', [ $this, 'purgeSessions'] );
	}

	/**
	 * Alias for delete()
	 *
	 * @return void
	 */
	public function clear()
	{
		$this->delete();
	}

	/**
	 * Delete the current session
	 *
	 * @return void
	 */
	public function delete()
	{
		$this->resetCookieExpiration();
		$this->regenerateSessionId( 'and delete session!' );
	}

	/**
	 * Get a variable from the current session
	 *
	 * @param string       $key
	 * @param string|array $fallback
	 * @param mixed        $unset
	 *
	 * @return string
	 */
	public function get( $key, $fallback = '', $unset = false )
	{
		$key = sanitize_key( $key );

		$value = isset( $this->session[ $key ] )
			? maybe_unserialize( $this->session[ $key ] )
			: $fallback;

		if( !!$unset ) {
			unset( $this->session[ $key ] );
			$this->updateSession();
		}

		return $value;
	}

	/**
	 * Reset the current session to an empty array, does not touch the database
	 *
	 * @return self
	 */
	public function reset()
	{
		$this->session = [];

		return $this;
	}

	/**
	 * Set a variable to the current session
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return string
	 */
	public function set( $key, $value )
	{
		$key = sanitize_key( $key );

		$this->session[ $key ] = maybe_serialize( $value );

		$this->updateSession();

		return $this->session[ $key ];
	}

	/**
	 * @return string
	 */
	protected function generateSessionId()
	{
		return md5((new PasswordHash( 8, false ))->get_random_bytes( 32 ));
	}

	/**
	 * @param mixed $purge
	 *
	 * @return void
	 */
	protected function regenerateSessionId( $purge = false )
	{
		$this->sessionId = $this->generateSessionId();
		$this->setCookie();

		if( !!$purge ) {
			$this->deleteSession();
		}
	}

	/**
	 * @return void
	 */
	protected function resetCookieExpiration()
	{
		$this->expiry      = time() + (30 * 60); // 30 minutes
		$this->expiryReset = time() + (24 * 60); // 24 minutes
	}

	/**
	 * @return void
	 */
	protected function setCookie()
	{
		$cookie = "{$this->sessionId}||{$this->expiry}||{$this->expiryReset}";

		$secure   = apply_filters( 'site-reviews/session/cookie/secure', false );
		$httponly = apply_filters( 'site-reviews/session/cookie/httponly', false );

		if( !defined( 'COOKIEPATH' )) {
			define( 'COOKIEPATH', preg_replace( '|https?://[^/]+|i', '', get_option( 'home' ) . '/' ));
		}

		if( !defined( 'COOKIE_DOMAIN' )) {
			define( 'COOKIE_DOMAIN', false );
		}

		if( !headers_sent() ) {
			setcookie( $this->app->id, $cookie, $this->expiry, COOKIEPATH, COOKIE_DOMAIN, $secure, $httponly );
		}
	}

	// DATABASE
	// ---------------------------------------------------------------------------------------------

	/**
	 * Delete ALL sessions from the database.
	 *
	 * @return int
	 */
	public function deleteSessions()
	{
		global $wpdb;

		$count = $wpdb->query(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$this->prefix}_%'"
		);

		return (int) ( $count / 2 );
	}

	/**
	 * Delete old sessions from the database.
	 *
	 * @param int $limit
	 *
	 * @return int
	 */
	public function purgeSessions( $limit = 1000 )
	{
		global $wpdb;

		$sessions = $wpdb->get_results( $wpdb->prepare(
			"SELECT option_name AS name, option_value AS expiry " .
			"FROM {$wpdb->options} " .
			"WHERE option_name LIKE '{$this->prefix}_expires_%' " .
			"ORDER BY option_value ASC " .
			"LIMIT 0, %d", absint( $limit )
		));

		if( empty( $sessions )) {
			return 0;
		}

		$now = time();
		$expired = [];
		$count = 0;

		foreach( $sessions as $session ) {
			if( $now <= $session->expiry )continue;

			$session_id = addslashes( substr( $session->name, 20 ));

			$expired[] = $session->name;
			$expired[] = "{$this->prefix}_{$session_id}";

			$count++;
		}

		// Delete expired sessions
		if( !empty( $expired )) {
			$session_names = implode( "','", $expired );

			$wpdb->query(
				"DELETE FROM {$wpdb->options} WHERE option_name IN ('{$session_names}')"
			);
		}

		return $count;
	}

	/**
	 * @return bool
	 */
	protected function createSession()
	{
		return add_option( "{$this->prefix}_{$this->sessionId}", $this->session, '', false );
	}

	/**
	 * @return bool
	 */
	protected function deleteSession()
	{
		return delete_option( "{$this->prefix}_{$this->sessionId}" );
	}

	/**
	 * @return array
	 */
	protected function getSession()
	{
		return $this->session = get_option( "{$this->prefix}_{$this->sessionId}", [] );
	}

	/**
	 * @return bool
	 */
	protected function resetSessionExpiration()
	{
		return update_option( "{$this->prefix}_expires_{$this->sessionId}", $this->expiry, false );
	}

	/**
	 * @return bool
	 */
	protected function updateSession()
	{
		if( false === get_option( "{$this->prefix}_{$this->sessionId}" )) {
			$this->resetSessionExpiration();
		}

		return update_option( "{$this->prefix}_{$this->sessionId}", $this->session, false );
	}
}
