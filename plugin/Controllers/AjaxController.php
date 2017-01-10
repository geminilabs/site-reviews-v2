<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Controllers\BaseController;
use GeminiLabs\SiteReviews\Commands\ChangeStatus;
use GeminiLabs\SiteReviews\Commands\TogglePinned;

class AjaxController extends BaseController
{
	/**
	 * Change a review status
	 *
	 * @since 2.0.0
	 */
	public function ajaxChangeReviewStatus( $request )
	{
		$response = $this->execute( new ChangeStatus( $request ));

		wp_send_json( $response );
	}

	/**
	 * Clears the log
	 */
	public function ajaxClearLog()
	{
		$this->app->make( 'Controllers\MainController' )->postClearLog();

		wp_send_json([
			'log'     => __( 'Log is empty', 'site-reviews' ),
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
			'pinned'  => (bool) $response,
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

	/**
	 * Load the shortcode dialog fields
	 *
	 * @param array $request
	 */
	public function ajaxMceShortcode( $request )
	{
		$shortcode = $request['shortcode'];

		if( array_key_exists( $shortcode, glsr_app()->mceShortcodes ) ) {

			$data = glsr_app()->mceShortcodes[ $shortcode ];

			if( !empty( $data['errors'] ) ) {
				$data['btn_okay'] = [ esc_html__( 'Okay', 'site-reviews' ) ];
			}

			$response = [
				'body'      => $data['fields'],
				'close'     => $data['btn_close'],
				'ok'        => $data['btn_okay'],
				'shortcode' => $shortcode,
				'title'     => $data['title'],
			];
		}
		else {
			$response = false;
		}

		wp_send_json( $response );
	}
}
