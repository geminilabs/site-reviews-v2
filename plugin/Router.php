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

class Router
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var AjaxController
	 */
	protected $ajax;

	protected $id;
	protected $prefix;

	public function __construct( App $app )
	{
		$this->app    = $app;
		$this->ajax   = $app->make( 'Controllers\AjaxController' );
		$this->id     = $app->id;
		$this->prefix = $app->prefix;
	}

	public function routeAjaxRequests()
	{
		$request = $_REQUEST['request'];

		if( isset( $request[ $this->prefix ]['action'] ) ) {
			$request = $request[ $this->prefix ];
		}

		// All ajax requests are triggered by a single action hook,
		// each route is determined by the request["action"].
		if( !isset( $request['action'] ) ) {
			wp_die();
		}

		$callback = function( $matches ) { return strtoupper( $matches[1] ); };
		$method   = preg_replace_callback( '/[-_](.)/', $callback, strtolower( 'ajax-' . $request['action'] ) );

		// Nonce url is localized in "GeminiLabs\SiteReviews\Handlers\EnqueueAssets"
		check_ajax_referer( "{$this->id}-ajax-nonce" );

		$request['ajax_request'] = true;

		if( is_callable([ $this->ajax, $method ]) ) {

			// undo damage done by javascript: encodeURIComponent()
			array_walk_recursive( $request, function( &$value ) {
				$value = stripslashes( $value );
			});

			$this->ajax->$method( $request );
		}

		wp_die();
	}

	public function routePostRequests()
	{
		// get the request data that is prefixed with the app prefix
		$request = filter_input( INPUT_POST, $this->prefix, FILTER_DEFAULT , FILTER_REQUIRE_ARRAY );

		if( !isset( $request['action'] ) )return;

		check_admin_referer( $request['action'] );

		$this->route( $request['action'] );
	}

	public function routeWebhookRequests()
	{
		$request = filter_input( INPUT_GET, "{$this->id}-hook" );

		if( !$request )return;

		// switch( $request ) {
		// 	default:break;
		// }
	}

	protected function route( $action )
	{
		switch( $action ) {
			case 'clear-log':
				$this->app->make( 'Controllers\MainController' )->postClearLog();
				break;

			case 'download-log':
				$this->app->make( 'Controllers\MainController' )->postDownloadLog();
				break;

			case 'download-system-info':
				$this->app->make( 'Controllers\MainController' )->postDownloadSystemInfo( $request['system-info'] );
				break;

			case 'post-review':
				$this->app->make( 'Controllers\ReviewController' )->postSubmitReview( $request );
				break;
		}
	}
}
