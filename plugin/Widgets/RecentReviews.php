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
			'show'        => [],
			'title'       => '',
		];

		$args = shortcode_atts( $defaults, $instance );

		$this->create_field([
			'type'  => 'text',
			'name'  => 'title',
			'label' => __( 'Title', 'geminilabs-site-reviews' ),
			'value' => $args['title'],
		]);

		$this->create_field([
			'type'  => 'select',
			'name'  => 'display',
			'class' => 'widefat',
			'value'   => $args['display'],
			'options' => [
				'title'   => __( 'Display only title', 'geminilabs-site-reviews' ),
				'excerpt' => __( 'Display only excerpt', 'geminilabs-site-reviews' ),
				'both'    => __( 'Display title and excerpt', 'geminilabs-site-reviews' ),
			],
		]);

		$this->create_field([
			'type'  => 'select',
			'name'  => 'min_rating',
			'label' => __( 'What is the minimum rating to display? ', 'geminilabs-site-reviews' ),
			'value'   => $args['min_rating'],
			'options' => [
				'5' => __( '5 stars', 'geminilabs-site-reviews' ),
				'4' => __( '4 stars', 'geminilabs-site-reviews' ),
				'3' => __( '3 stars', 'geminilabs-site-reviews' ),
				'2' => __( '2 stars', 'geminilabs-site-reviews' ),
				'1' => __( '1 star', 'geminilabs-site-reviews' ),
			],
		]);

		$this->create_field([
			'type'    => 'number',
			'name'    => 'max_reviews',
			'label'   => __( 'How many reviews would you like to display? ', 'geminilabs-site-reviews' ),
			'value'   => $args['max_reviews'],
			'default' => 5,
			'max'     => 100,
		]);

		$this->create_field([
			'type'  => 'checkbox',
			'name'  => 'show',
			'value' => $args['show'],
			'options' => [
				'show_author' => __( 'Show the name of the reviewer?', 'geminilabs-site-reviews' ),
				'show_date'   => __( 'Show the review date?', 'geminilabs-site-reviews' ),
				'show_rating' => __( 'Show the review rating?', 'geminilabs-site-reviews' ),
			],
		]);

		$this->create_field([
			'type'  => 'text',
			'name'  => 'class',
			'label' => __( 'Enter any custom CSS classes here', 'geminilabs-site-reviews' ),
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
		];

		$instance = shortcode_atts( $defaults, $instance );

		foreach( $instance['show'] as $key ) {
			$instance[ $key ] = true;
		}

		unset( $instance['show'] );

		$instance['order_by'] ?: $instance['order_by'] = 'date';
		$instance['site_name'] = 'local';

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		echo $title ? $args['before_title'] . $title . $args['after_title'] : '';

		glsr_resolve( 'Html' )->renderPartial( 'reviews', $instance );

		echo $args['after_widget'];
	}
}
