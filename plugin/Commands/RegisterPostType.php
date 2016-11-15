<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Commands;

class RegisterPostType
{
	public $args;

	public function __construct( $input )
	{
		$columns = [
			'title' => __( 'Title', 'site-reviews' ),
			'date'  => __( 'Date', 'site-reviews' ),
		];

		$defaults = [
			'capability_type' => '',
			'columns'         => $columns,
			'has_archive'     => false,
			'hierarchical'    => false,
			'map_meta_cap'    => true,
			'menu_icon'       => null,
			'menu_name'       => '',
			'menu_position'   => 25,
			'public'          => true,
			'query_var'       => true,
			'rewrite'         => ['slug' => $input['slug'], 'with_front' => false ],
			'show_in_menu'    => true, //'edit.php?post_type=post'
			'supports'        => ['title', 'editor'],
			'taxonomies'      => [],
		];

		$this->args = wp_parse_args( $input, $defaults );
	}
}
