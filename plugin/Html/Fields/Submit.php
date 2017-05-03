<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Fields;

use GeminiLabs\SiteReviews\Html\Fields\Text;

class Submit extends Text
{
	/**
	 * @return string
	 */
	public function render()
	{
		if( isset( $this->args['name'] )) {
			$this->args['name'] = 'submit';
		}

		return $this->recaptcha() . parent::render([
			'class'  => 'button button-primary',
			'type'   => 'submit',
		]);
	}

	/**
	 * @return string
	 */
	protected function recaptcha()
	{
		if( glsr_get_option( 'reviews-form.recaptcha.enabled' ) != 'yes' )return;
		return sprintf( '<div class="glsr-recaptcha-holder" data-sitekey="%s" data-badge="%s" data-size="invisible"></div>',
			sanitize_text_field( glsr_get_option( 'reviews-form.recaptcha.key' )),
			sanitize_text_field( glsr_get_option( 'reviews-form.recaptcha.position' ))
		);
	}
}
