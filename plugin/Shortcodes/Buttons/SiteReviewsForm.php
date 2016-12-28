<?php

/**
 * = Site Reviews Form shortcode button
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.3.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes\Buttons;

use GeminiLabs\SiteReviews\Shortcodes\Buttons\Generator;

class SiteReviewsForm extends Generator
{
	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			[
				'type' => 'container',
				'html' => sprintf( '<p class="strong">%s</p>', esc_html__( 'Optional settings', 'site-reviews' ) ),
			],[
				'type'     => 'textbox',
				'name'     => 'title',
				'label'    => esc_html__( 'Title:', 'site-reviews' ),
				'minWidth' => 320,
				'tooltip'  => esc_attr__( 'Enter a title', 'site-reviews' ),
			],[
				'type'    => 'listbox',
				'name'    => 'rating',
				'label'   => esc_html__( 'Rating:', 'site-reviews' ),
				'options' => [
					'5' => esc_html__( '5 stars', 'site-reviews' ),
					'4' => esc_html__( '4 stars', 'site-reviews' ),
					'3' => esc_html__( '3 stars', 'site-reviews' ),
					'2' => esc_html__( '2 stars', 'site-reviews' ),
					'1' => esc_html__( '1 star', 'site-reviews' ),
				],
				'tooltip' => esc_attr__( 'What is the minimum rating to display?', 'site-reviews' ),
			],[
				'type'     => 'textbox',
				'name'     => 'count',
				'label'    => esc_html__( 'Display:', 'site-reviews' ),
				'tooltip'  => esc_attr__( 'How many reviews would you like to display?', 'site-reviews' ),
			],[
				'type'     => 'textbox',
				'name'     => 'class',
				'label'    => esc_html__( 'Classes:', 'site-reviews' ),
				'minWidth' => 320,
				'tooltip'  => esc_attr__( 'Enter any custom CSS classes here.', 'site-reviews' ),
			],[
				'type'    => 'listbox',
				'name'    => 'pagination',
				'label'   => esc_html__( 'Pagination:', 'site-reviews' ),
				'options' => [
					'true'  => esc_html__( 'Enable', 'site-reviews' ),
					'false' => esc_html__( 'Disable', 'site-reviews' ),
				],
				'tooltip' => esc_attr__( 'When using pagination this shortcode can only be used once on a page.', 'site-reviews' ),
			],
			[
				'label' => esc_html__( 'Display:', 'site-reviews' ),
				'type' => 'checkbox',
				'name' => 'constrain',
				'checked' => true,
				'text' => 'Title',
			],
			[
				'type' => 'container',
				'label' => 'Dimensions',
				'layout' => 'flex',
				'direction' => 'row',
				'align' => 'center',
				'spacing' => 5,
				'items' => [
					[
						'name' => 'width',
						'type' => 'textbox',
						'maxLength' => 5,
						'size' => 3,
					],[
						'type' => 'label',
						'text' => 'x',
					],[
						'name' => 'height',
						'type' => 'textbox',
						'maxLength' => 5,
						'size' => 3,
					],[
						'name' => 'constrain',
						'type' => 'checkbox',
						'checked' => true,
						'text' => 'Constrain proportions',
					],
				],
			],
		];
	}
}
