<?php

/**
 * = Reviews Form shortcode
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcode;

class SiteReviewsForm extends Shortcode
{
	/**
	 * @return null|string
	 */
	public function printShortcode( $atts = [] )
	{
		$requireUser = glsr_resolve( 'Database' )->getOption( 'general.require.login', false );

		if( $requireUser && !is_user_logged_in() ) {
			$message = sprintf(
				__( 'You must be <a href="%s">logged in</a> to submit a review.', 'site-reviews' ),
				wp_login_url( get_permalink() )
			);
			echo wpautop( $message );
			return;
		}

		$defaults = [
			'class'       => '',
			'description' => '',
			'hide'        => '',
			'title'       => '',
		];

		$atts = shortcode_atts( $defaults, $atts );

		$fields = explode( ',', $atts['hide'] );
		$fields = array_filter( $fields, function( $value ) {
			return in_array( $value, [
				'title',
				'reviewer',
				'email',
				'terms'
			]);
		});

		$formId = $this->generate_id( $atts );

		$errors  = $this->session->get( "{$formId}-errors", [], 'and then remove errors' );
		$message = $this->session->get( "{$formId}-message", [], 'and then remove message' );

		$values  = !empty( $errors )
			? $this->session->get( "{$formId}-values", [], 'and then remove values' )
			: [];

		ob_start();

		echo '<div class="shortcode-reviews-form">';

		if( !empty( $atts['title'] ) ) {
			printf( '<h3 class="glsr-form-title">%s</h3>', $atts['title'] );
		}

		if( !empty( $atts['description'] ) ) {
			printf( '<p class="glsr-form-description">%s</p>', $atts['description'] );
		}

		$this->app->make( 'Controllers\ReviewController' )->render( 'submit/index', [
			'class'   => trim( 'glsr-submit-review-form ' . $atts['class'] ),
			'errors'  => $errors,
			'exclude' => $fields,
			'form_id' => $formId,
			'message' => $message,
			'values'  => shortcode_atts([
				'rating'   => '',
				'title'    => '',
				'content'  => '',
				'reviewer' => '',
				'email'    => '',
				'terms'    => '',
			], $values ),
		]);

		echo '</div>';

		return ob_get_clean();
	}
}
