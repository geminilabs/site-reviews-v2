<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv2 or later
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Database;

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
	 * @param mixed  $args ...
	 *
	 * @return mixed
	 */
	public function get( $name, $args )
	{
		$method = 'get' . ucfirst( strtolower( $name ));

		if( !method_exists( $this, $method ))return;

		return call_user_func_array([ $this, $method ], array_slice( func_get_args(), 1 ));
	}

	/**
	 * @param string $optionPath
	 * @param mixed  $fallback
	 *
	 * @return mixed
	 */
	protected function getOption( $optionPath, $fallback )
	{
		return $this->db->getOption( $optionPath, $fallback );
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
