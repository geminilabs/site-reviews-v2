<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv2 or later
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Commands;

class RegisterTaxonomies
{
	public $taxonomies;

	public function __construct( $input )
	{
		$this->taxonomies = [];

		$defaults = [
			'hierarchical'      => true,
			'menu_name'         => '',
			'post_type'         => '',
			'prepend_post_type' => true,
			'public'            => true,
			'rewrite'           => true,
			'show_admin_column' => true,
		];

		foreach( $input as $taxonomy => $args ) {

			$args = wp_parse_args( $args, $defaults );

			$defaults = [
				'publicly_queryable' => $args['public'],
				'show_in_nav_menus'  => $args['public'],
				'show_ui'            => $args['public'],
			];

			$this->taxonomies[ $taxonomy ] = wp_parse_args( $args, $defaults );
		}
	}
}
