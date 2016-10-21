<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Commands;

class SubmitReview
{
	public $ajaxRequest;
	public $content;
	public $email;
	public $formId;
	public $ipAddress;
	public $rating;
	public $reviewer;
	public $terms;
	public $title;

	public function __construct( $input )
	{
		$this->ajaxRequest = isset( $input['ajax_request'] ) ? true : false;
		$this->content     = $input['content'];
		$this->email       = $input['email'];
		$this->formId      = $input['form_id'];
		$this->ipAddress   = $this->getIpAddress();
		$this->rating      = $input['rating'];
		$this->reviewer    = $input['reviewer'];
		$this->title       = $input['title'];
		$this->terms       = isset( $input['terms'] ) ? true : false;
	}

	/**
	 * Get the IP address and domain of the reviewer
	 *
	 * @return string|null
	 */
	protected function getIpAddress()
	{
		$ipAddress = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;

		if( $ipAddress ) {
			$ipAddress .= ', ' . @gethostbyaddr( $ipAddress );
		}

		return $ipAddress;
	}
}
