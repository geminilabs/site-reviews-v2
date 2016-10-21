<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\SubmitReview;
use GeminiLabs\SiteReviews\Controllers\BaseController;

class ReviewController extends BaseController
{
	/**
	 * Submit the review form
	 *
	 * @return void
	 * @throws Exception
	 */
	public function postSubmitReview( array $request )
	{
		$minContentLength = apply_filters( 'site-reviews/local/review/content/minLength', '0' );

		$rules = [
			'content'  => 'required|min:' . $minContentLength,
			'email'    => 'required|email|min:5',
			'rating'   => 'required|numeric|between:1,5',
			'reviewer' => 'required',
			'terms'    => 'accepted',
			'title'    => 'required',
		];

		$excluded = isset( $request['excluded'] )
			? json_decode( $request['excluded'] )
			: [];

		// only use the rules for non-excluded values
		$rules = array_diff_key( $rules, array_flip( $excluded ) );

		$defaults = [
			'content'  => '',
			'email'    => '',
			'rating'   => '',
			'reviewer' => __( 'Anonymous', 'geminilabs-site-reviews' ),
			'terms'    => '',
			'title'    => __( 'No Title', 'geminilabs-site-reviews' ),
		];

		if( !$this->validate( $request, $rules ) ) {
			return __( 'Please fix the submission errors.', 'geminilabs-site-reviews' );
		}

		// normalize the request array
		$request = array_merge( $defaults, $request );

		return $this->execute( new SubmitReview( $request ) );
	}
}

