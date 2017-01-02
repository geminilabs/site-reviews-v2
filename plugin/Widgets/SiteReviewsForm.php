<?php

/**
 * Site Reviews Form widget
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Traits\SiteReviewsForm as Common;
use GeminiLabs\SiteReviews\Widget;

class SiteReviewsForm extends Widget
{
	use Common;

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
			'hide'        => [],
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
			'name'  => 'hide',
			'value' => $args['hide'],
			'options' => [
				'email' => __( 'Hide the email field', 'site-reviews' ),
				'name'  => __( 'Hide the name field', 'site-reviews' ),
				'terms' => __( 'Hide the terms field', 'site-reviews' ),
				'title' => __( 'Hide the title field', 'site-reviews' ),
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
			'hide'        => [],
			'title'       => '',
		];

		// custom widget attributes
		$instance = shortcode_atts( $defaults, $instance );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		if( !empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if( !$this->renderRequireLogin() ) {
			echo $this->renderForm( $instance );
		}

		echo $args['after_widget'];
	}
}
