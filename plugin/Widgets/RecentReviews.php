<?php

/**
 * Recent Reviews widget
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Widget;

class RecentReviews extends Widget
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
			'display'     => 'both',
			'max_reviews' => 5,
			'min_rating'  => '',
			'order_by'    => '',
			'show'        => ['show_author', 'show_date', 'show_rating'],
			'title'       => '',
			'type'        => '',
		];

		if( !empty( $instance ) ) {
			isset( $instance['show'] ) ?: $instance['show'] = [];
		}

		$args = shortcode_atts( $defaults, $instance );

		$this->create_field([
			'type'  => 'text',
			'name'  => 'title',
			'label' => __( 'Title', 'site-reviews' ),
			'value' => $args['title'],
		]);

		$this->create_field([
			'type'  => 'select',
			'name'  => 'display',
			'class' => 'widefat',
			'value'   => $args['display'],
			'options' => [
				'title'   => __( 'Display only title', 'site-reviews' ),
				'excerpt' => __( 'Display only excerpt', 'site-reviews' ),
				'both'    => __( 'Display title and excerpt', 'site-reviews' ),
			],
		]);

		$types = glsr_resolve( 'Database' )->getReviewTypes();

		if( count( $types ) > 1 ) {
			$this->create_field([
				'type'  => 'select',
				'name'  => 'type',
				'label' => __( 'Which reviews would you like to display? ', 'site-reviews' ),
				'value'   => $args['type'],
				'options' => ['' => __( 'All Reviews', 'site-reviews' ) ] + $types,
			]);
		}

		$this->create_field([
			'type'  => 'select',
			'name'  => 'min_rating',
			'label' => __( 'What is the minimum rating to display? ', 'site-reviews' ),
			'value'   => $args['min_rating'],
			'options' => [
				'5' => __( '5 stars', 'site-reviews' ),
				'4' => __( '4 stars', 'site-reviews' ),
				'3' => __( '3 stars', 'site-reviews' ),
				'2' => __( '2 stars', 'site-reviews' ),
				'1' => __( '1 star', 'site-reviews' ),
			],
		]);

		$this->create_field([
			'type'    => 'number',
			'name'    => 'max_reviews',
			'label'   => __( 'How many reviews would you like to display? ', 'site-reviews' ),
			'value'   => $args['max_reviews'],
			'default' => 5,
			'max'     => 100,
		]);

		$this->create_field([
			'type'  => 'checkbox',
			'name'  => 'show',
			'value' => $args['show'],
			'options' => [
				'show_author' => __( 'Show the name of the reviewer?', 'site-reviews' ),
				'show_date'   => __( 'Show the review date?', 'site-reviews' ),
				'show_rating' => __( 'Show the review rating?', 'site-reviews' ),
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
	 * Update the widget form
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		if( $new_instance['max_reviews'] < 0 ) {
			$new_instance['max_reviews'] = 0;
		}

		if( $new_instance['max_reviews'] > 100 ) {
			$new_instance['max_reviews'] = 100;
		}

		return parent::update( $new_instance, $old_instance );
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
			'display'     => 'both',
			'max_reviews' => '10',
			'min_rating'  => '5',
			'order_by'    => 'date',
			'show'        => [],
			'title'       => '',
			'type'        => '',
		];

		$instance = shortcode_atts( $defaults, $instance );

		foreach( $instance['show'] as $key ) {
			$instance[ $key ] = true;
		}

		unset( $instance['show'] );

		$instance['order_by'] ?: $instance['order_by'] = 'date';

		$instance['site_name'] = $instance['type'];

		unset( $instance['type'] );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		echo $title ? $args['before_title'] . $title . $args['after_title'] : '';

		glsr_resolve( 'Html' )->renderPartial( 'reviews', $instance );

		echo $args['after_widget'];
	}
}
