<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Commands;

class SubmitReview
{
	public $ajaxRequest;
	public $assignedTo;
	public $author;
	public $category;
	public $content;
	public $email;
	public $formId;
	public $ipAddress;
	public $rating;
	public $referrer;
	public $request;
	public $terms;
	public $title;

	public function __construct( $input )
	{
		$this->ajaxRequest = isset( $input['ajax_request'] ) ? true : false;
		$this->assignedTo  = is_numeric( $input['assign_to'] ) ? $input['assign_to'] : '';
		$this->author      = $input['name'];
		$this->category    = $input['category'];
		$this->content     = $input['content'];
		$this->email       = $input['email'];
		$this->formId      = $input['form_id'];
		$this->ipAddress   = glsr_resolve( 'Helper' )->getIpAddress();
		$this->rating      = intval( $input['rating'] );
		$this->referrer    = $input['_wp_http_referer'];
		$this->request     = $input;
		$this->terms       = isset( $input['terms'] ) ? true : false;
		$this->title       = $input['title'];
	}
}
