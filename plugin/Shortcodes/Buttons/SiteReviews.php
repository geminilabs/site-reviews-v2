<?php

/**
 * Site Reviews shortcode button
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes\Buttons;

use GeminiLabs\SiteReviews\Shortcodes\Buttons\Generator;

class SiteReviews extends Generator
{
	/**
	 * @return array
	 */
	public function fields()
	{
		$types = glsr_resolve( 'Database' )->getReviewTypes();

		if( count( $types ) > 1 ) {
			$display = [
				'type'    => 'listbox',
				'name'    => 'display',
				'label'   => esc_html__( 'Display', 'site-reviews' ),
				'options' => $types,
				'tooltip' => esc_attr__( 'Which reviews would you like to display?', 'site-reviews' ),
			];
		}

		return [
			[
				'type' => 'container',
				'html' => sprintf( '<p class="strong">%s</p>', esc_html__( 'All settings are optional.', 'site-reviews' ) ),
				'minWidth' => 320,
			],[
				'type'     => 'textbox',
				'name'     => 'title',
				'label'    => esc_html__( 'Title', 'site-reviews' ),
				'tooltip'  => esc_attr__( 'Enter a custom shortcode heading.', 'site-reviews' ),
			],[
				'type'      => 'textbox',
				'name'      => 'count',
				'maxLength' => 5,
				'size'      => 3,
				'text'      => '10',
				'label'     => esc_html__( 'Count', 'site-reviews' ),
				'tooltip'   => esc_attr__( 'How many reviews would you like to display (default: 10)?', 'site-reviews' ),
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
				'tooltip' => esc_attr__( 'What is the minimum rating to display?', 'site-reviews' ),
			],
				( isset( $display ) ? $display : [] ),
			[
				'type'    => 'listbox',
				'name'    => 'category',
				'label'   => esc_html__( 'Category', 'site-reviews' ),
				'options' => glsr_resolve( 'Database' )->getTerms(),
				'tooltip' => esc_attr__( 'Limit reviews to this category.', 'site-reviews' ),
			],[
				'type'    => 'listbox',
				'name'    => 'pagination',
				'label'   => esc_html__( 'Pagination', 'site-reviews' ),
				'options' => [
					'true'  => esc_html__( 'Enable', 'site-reviews' ),
					'false' => esc_html__( 'Disable', 'site-reviews' ),
				],
				'tooltip' => esc_attr__( 'When using pagination this shortcode can only be used once on a page.', 'site-reviews' ),
			],[
				'type'     => 'textbox',
				'name'     => 'class',
				'label'    => esc_html__( 'Classes', 'site-reviews' ),
				'tooltip'  => esc_attr__( 'Add custom CSS classes to the shortcode.', 'site-reviews' ),
			],[
				'type'    => 'container',
				'label'   => esc_html__( 'Hide', 'site-reviews' ),
				'layout'  => 'grid',
				'columns' => 2,
				'spacing' => 5,
				'items'   => [
					[
						'type' => 'checkbox',
						'name' => 'hide_author',
						'text' => esc_html__( 'Author', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the review author?', 'site-reviews' ),
					],[
						'type' => 'checkbox',
						'name' => 'hide_date',
						'text' => esc_html__( 'Date', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the review date?', 'site-reviews' ),
					],[
						'type' => 'checkbox',
						'name' => 'hide_excerpt',
						'text' => esc_html__( 'Excerpt', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the review excerpt?', 'site-reviews' ),
					],[
						'type' => 'checkbox',
						'name' => 'hide_rating',
						'text' => esc_html__( 'Rating', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the review rating?', 'site-reviews' ),
					],[
						'type' => 'checkbox',
						'name' => 'hide_title',
						'text' => esc_html__( 'Title', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the review title?', 'site-reviews' ),
					],
				],
			],
		];
	}
}
