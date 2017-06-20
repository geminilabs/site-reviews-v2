<?php

/**
 * Site Reviews Summary shortcode button
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.3.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes\Buttons;

use GeminiLabs\SiteReviews\Shortcodes\Buttons\Generator;

class SiteReviewsSummary extends Generator
{
	/**
	 * @return array
	 */
	public function fields()
	{
		$types = glsr_resolve( 'Database' )->getReviewTypes();
		$terms = glsr_resolve( 'Database' )->getTerms();

		if( count( $types ) > 1 ) {
			$display = [
				'type'    => 'listbox',
				'name'    => 'display',
				'label'   => esc_html__( 'Display', 'site-reviews' ),
				'options' => $types,
				'tooltip' => __( 'Which reviews would you like to display?', 'site-reviews' ),
			];
		}
		if( !empty( $terms )) {
			$category = [
				'type'    => 'listbox',
				'name'    => 'category',
				'label'   => esc_html__( 'Category', 'site-reviews' ),
				'options' => $terms,
				'tooltip' => __( 'Limit reviews to this category.', 'site-reviews' ),
			];
		}
		return [
			[
				'type' => 'container',
				'html' => sprintf( '<p class="strong">%s</p>', esc_html__( 'All settings are optional.', 'site-reviews' )),
				'minWidth' => 320,
			],[
				'type'     => 'textbox',
				'name'     => 'title',
				'label'    => esc_html__( 'Title', 'site-reviews' ),
				'tooltip'  => __( 'Enter a custom shortcode heading.', 'site-reviews' ),
			],[
				'type'     => 'textbox',
				'name'     => 'labels',
				'label'    => esc_html__( 'Labels', 'site-reviews' ),
				'tooltip'  => __( 'Enter custom labels for the 1-5 star rating levels (from high to low), and separate each with a comma. The defaults labels are: "Excellent,Very good,Average,Poor,Terrible".', 'site-reviews' ),
			],[
				'type'    => 'listbox',
				'name'    => 'rating',
				'label'   => esc_html__( 'Rating', 'site-reviews' ),
				'options' => [
					'5' => esc_html__( '5 stars', 'site-reviews' ),
					'4' => esc_html__( '4 stars', 'site-reviews' ),
					'3' => esc_html__( '3 stars', 'site-reviews' ),
					'2' => esc_html__( '2 stars', 'site-reviews' ),
					'1' => esc_html__( '1 star', 'site-reviews' ),
				],
				'tooltip' => __( 'What is the minimum rating?', 'site-reviews' ),
			],
			( isset( $display ) ? $display : [] ),
			( isset( $category ) ? $category : [] ),
			[
				'type'      => 'textbox',
				'name'      => 'assigned_to',
				'label'     => esc_html__( 'Post ID', 'site-reviews' ),
				'tooltip'   => __( "Limit reviews to those assigned to this post ID (separate multiple ID's with a comma).", 'site-reviews' ),
			],[
				'type'     => 'textbox',
				'name'     => 'class',
				'label'    => esc_html__( 'Classes', 'site-reviews' ),
				'tooltip'  => __( 'Add custom CSS classes to the shortcode.', 'site-reviews' ),
			],
		];
	}
}
