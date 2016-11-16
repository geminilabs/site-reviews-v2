<?php

/**
 * Reviews Form widget
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Widget;

class ReviewsForm extends Widget
{
	/**
	 * Display the widget form
	 *
	 * @param array $instance
	 *
	 * @return void
	 */
	public function form( $instance )
	{
		$defaults = [
			'class'       => '',
			'description' => sprintf( __( 'Your email address will not be published. Required fields are marked %s*%s', 'site-reviews' ), '<span>', '</span>' ),
			'fields'      => [],
			'title'       => '',
		];

		$args = shortcode_atts( $defaults, $instance );

		$this->create_field([
			'type'  => 'text',
			'name'  => 'title',
			'label' => __( 'Title', 'site-reviews' ),
			'value' => $args['title'],
		]);

		$this->create_field([
			'type'  => 'textarea',
			'name'  => 'description',
			'class' => 'widefat',
			'label' => __( 'Description', 'site-reviews' ),
			'value' => $args['description'],
		]);

		$this->create_field([
			'type'  => 'checkbox',
			'name'  => 'fields',
			'value' => $args['fields'],
			'options' => [
				'title'    => __( 'Hide the title field', 'site-reviews' ),
				'reviewer' => __( 'Hide the reviewer field', 'site-reviews' ),
				'email'    => __( 'Hide the email field', 'site-reviews' ),
				'terms'    => __( 'Hide the terms field', 'site-reviews' ),
			],
		]);

		$this->create_field([
			'type'  => 'text',
			'name'  => 'class',
			'label' => __( 'Enter any custom CSS classes here', 'site-reviews' ),
			'value' => $args['class'],
		]);
	}

	/**
	 * Display the widget Html
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		$defaults = [
			'class'       => '',
			'description' => '',
			'fields'      => [],
			'title'       => '',
		];

		// custom widget attributes
		$instance = shortcode_atts( $defaults, $instance );

		$controller = glsr_resolve( 'Controllers\ReviewController' );
		$session    = glsr_resolve( 'Session' );

		$defaults = [
			'content'  => '',
			'email'    => '',
			'rating'   => '',
			'reviewer' => '',
			'terms'    => '',
			'title'    => '',
		];

		$formId = $this->generate_id();

		$errors  = $session->get( "{$formId}-errors", [], 'and then remove errors' );
		$message = $session->get( "{$formId}-message", [], 'and then remove message' );

		$values  = !empty( $errors )
			? $this->session->get( "{$formId}-values", [], 'and then remove values' )
			: [];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$description = apply_filters( 'widget_description', $instance['description'], $instance, $this->id_base );

		echo $args['before_widget'];

		echo $title ? $args['before_title'] . $title . $args['after_title'] : '';

		$requireUser = glsr_resolve( 'Database' )->getOption( 'general.require.login', false );

		if( $requireUser && !is_user_logged_in() ) {
			$message = sprintf(
				__( 'You must be <a href="%s">logged in</a> to submit a review.', 'site-reviews' ),
				wp_login_url( get_permalink() )
			);
			echo wpautop( $message );
			return;
		}
		else {
			echo $description ? sprintf( '<p class="glsr-form-description">%s</p>', $description ) : '';

			$controller->render( 'submit/index', [
				'class'   => trim( 'glsr-submit-review-form ' . $instance['class'] ),
				'errors'  => $errors,
				'exclude' => $instance['fields'],
				'form_id' => $formId,
				'message' => $message,
				'values'  => shortcode_atts( $defaults, $values ),
			]);
		}

		echo $args['after_widget'];
	}
}
