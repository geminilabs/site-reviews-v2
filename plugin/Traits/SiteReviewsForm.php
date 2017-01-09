<?php

/**
 * Shared shortcode/widget methods
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Traits;

trait SiteReviewsForm
{
	/**
	 * @var bool|string
	 */
	public $id = false;

	/**
	 * Generate a unique ID string
	 *
	 * @param mixed $from
	 *
	 * @return string
	 */
	public function generateId( $from = [] )
	{
		if( $this->id ) {
			$from = $this->id;
		}

		return substr( md5( serialize( $from ) ), 0, 8 );
	}

	/**
	 * @return string
	 */
	public function renderForm( array $atts )
	{
		$formId  = $this->generateId( $atts );
		$session = glsr_resolve( 'Session' );
		$errors  = $session->get( "{$formId}-errors", [], 'and then remove errors' );
		$message = $session->get( "{$formId}-message", [], 'and then remove message' );

		$values  = !empty( $errors )
			? $session->get( "{$formId}-values", [], 'and then remove values' )
			: [];

		ob_start();

		if( !empty( $atts['description'] ) ) {
			printf( '<p class="glsr-form-description">%s</p>', $atts['description'] );
		}

		glsr_resolve( 'Controllers\ReviewController' )->render( 'submit/index', [
			'category' => $atts['category'],
			'class'    => trim( 'glsr-submit-review-form ' . $atts['class'] ),
			'errors'   => $errors,
			'exclude'  => $atts['hide'],
			'form_id'  => $formId,
			'message'  => $message,
			'values'   => shortcode_atts([
				'content' => '',
				'email'   => '',
				'name'    => '',
				'rating'  => '',
				'terms'   => '',
				'title'   => '',
			], $values ),
		]);

		return ob_get_clean();
	}

	/**
	 * @return bool|string
	 */
	public function renderRequireLogin()
	{
		$requireUser = glsr_resolve( 'Database' )->getOption( 'settings.general.require.login' );

		if( $requireUser == 'yes' && !is_user_logged_in() ) {
			$message = sprintf(
				__( 'You must be <a href="%s">logged in</a> to submit a review.', 'site-reviews' ),
				wp_login_url( get_permalink() )
			);

			echo wpautop( $message );

			return true;
		}

		return false;
	}
}
