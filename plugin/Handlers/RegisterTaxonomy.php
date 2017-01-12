<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Commands\RegisterTaxonomy as Command;

class RegisterTaxonomy
{
	/**
	 * @var App
	 */
	protected $app;

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		register_taxonomy( $this->app->taxonomy, $this->app->post_type, $command->args );

		register_taxonomy_for_object_type( $this->app->taxonomy, $this->app->post_type );

		add_action( "{$this->app->taxonomy}_term_edit_form_top", [ $this, 'disableParents'] );
		add_action( "{$this->app->taxonomy}_term_new_form_tag",  [ $this, 'disableParents'] );
		add_action( "{$this->app->taxonomy}_add_form_fields",    [ $this, 'enableParents'] );
		add_action( "{$this->app->taxonomy}_edit_form",          [ $this, 'enableParents'] );
		add_action( 'restrict_manage_posts',                     [ $this, 'renderFilterTaxonomy'], 9 );
	}

	/**
	 * @return void
	 *
	 * @action {$taxonomy}_add_form_fields
	 * @action {$taxonomy}_edit_form
	 */
	public function disableParents()
	{
		global $wp_taxonomies;
		$wp_taxonomies[ $this->app->taxonomy ]->hierarchical = false;
	}

	/**
	 * @return void
	 *
	 * @action {$taxonomy}_term_edit_form_top
	 * @action {$taxonomy}_term_new_form_tag
	 */
	public function enableParents()
	{
		global $wp_taxonomies;
		$wp_taxonomies[ $this->app->taxonomy ]->hierarchical = true;
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

		if( apply_filters( 'site-reviews/disable/filter/category', false )
			|| !is_object_in_taxonomy( get_current_screen()->post_type, $this->app->taxonomy )
		)return;

		printf( '<label class="screen-reader-text" for="%s">%s</label>', $this->app->taxonomy, __( 'Filter by category', 'site-reviews' ));

		$selected = isset( $wp_query->query[ $this->app->taxonomy ] )
			? $wp_query->query[ $this->app->taxonomy ]
			: '';

		wp_dropdown_categories([
			'depth'           => 3,
			'hide_empty'      => true,
			'hide_if_empty'   => true,
			'hierarchical'    => true,
			'name'            => $this->app->taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => false,
			'show_option_all' => ucfirst( strtolower( get_taxonomy( $this->app->taxonomy )->labels->all_items )),
			'taxonomy'        => $this->app->taxonomy,
			'value_field'     => 'slug',
		]);
	}
}
