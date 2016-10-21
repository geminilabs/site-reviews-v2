<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Controllers\BaseController;
use GeminiLabs\SiteReviews\Commands\TogglePinned;

class AjaxController extends BaseController
{
	/**
	 * Clears the log
	 */
	public function ajaxClearLog()
	{
		$this->app->make( 'Controllers\MainController' )->postClearLog();

		wp_send_json([
			'log'     => __( 'Log is empty', 'geminilabs-site-reviews' ),
			'notices' => $this->notices->show( false ),
		]);
	}

	/**
	 * Toggle the pinned status of a review
	 */
	public function ajaxTogglePinned( $request )
	{
		$response = $this->execute( new TogglePinned( $request ) );

		wp_send_json([
			'notices' => $this->notices->show( false ),
			'pinned'  => $response,
		]);
	}

	/**
	 * Submit a review
	 */
	public function ajaxPostReview( $request )
	{
		$response = $this->app->make( 'Controllers\ReviewController' )->postSubmitReview( $request );
		$errors   = $this->app->make( 'Session' )->get( "{$request['form_id']}-errors", false, 'clear errors' );

		wp_send_json([
			'errors'  => $errors,
			'message' => $response,
		]);
	}
}
