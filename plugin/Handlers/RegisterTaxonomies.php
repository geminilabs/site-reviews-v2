<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv2 or later
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\RegisterTaxonomies as Command;

class RegisterTaxonomies
{
	/**
	 * @var array
	 */
	protected $taxonomies;

	public function handle( Command $command )
	{
		$taxonomies = [];

		foreach( $command->taxonomies as $taxonomy => $args ) {

			$taxonomies[] = $taxonomy;

			register_taxonomy( $taxonomy, $args['post_type'], $args );

			foreach( (array) $args['post_type'] as $post_type ) {
				register_taxonomy_for_object_type( $taxonomy, $post_type );
			}
		}

		$this->taxonomies = array_keys( array_flip( $taxonomies ) );

		add_action( 'restrict_manage_posts', [ $this, 'renderFilterTaxonomy'], 9 );
	}

	/**
	 * Create the Taxonomy filter dropdown
	 *
	 * @return void
	 *
	 * @action restrict_manage_posts
	 */
	public function renderFilterTaxonomy()
	{
		global $wp_query;

		$screen = get_current_screen();

		foreach( $this->taxonomies as $taxonomy ) {

			if( !is_object_in_taxonomy( $screen->post_type, $taxonomy ) )continue;

			if( apply_filters( 'site-reviews/disable/filter/category', false, $taxonomy, $screen->post_type ) )return;

			printf( '<label class="screen-reader-text" for="%s">%s</label>', $taxonomy, __( 'Filter by category', 'site-reviews' ) );

			$selected = isset( $wp_query->query[ $taxonomy ] )
				? $wp_query->query[ $taxonomy ]
				: '';

			wp_dropdown_categories([
				'depth'           => 3,
				'hide_empty'      => false,
				'hierarchical'    => true,
				'name'            => $taxonomy,
				'orderby'         => 'name',
				'selected'        => $selected,
				'show_count'      => true,
				'show_option_all' => ucfirst( strtolower( get_taxonomy( $taxonomy )->labels->all_items ) ),
				'taxonomy'        => $taxonomy,
			]);
		}
	}
}
