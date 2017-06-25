<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Database;

class Upgrade
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Database
	 */
	protected $db;

	public function __construct( App $app, Database $db )
	{
		$this->app = $app;
		$this->db  = $db;
	}

	/**
	 * Migrate plugin options
	 *
	 * @return void
	 */
	public function options_200()
	{
		$defaults = [
			'last_fetch'            => [],
			'logging'               => 0,
			'settings'              => [],
			'version'               => '',
			'version_upgraded_from' => '',
		];

		$oldOptions = [];

		foreach( $defaults as $key => $fallback ) {
			$optionName = sprintf( '%s_%s', $this->app->prefix, $key );
			$oldOptions[ $key ] = get_option( $optionName, $fallback );
			delete_option( $optionName );
		}

		// migrate "form" settings page
		if( isset( $oldOptions['settings']['form'] )) {
			$oldOptions['settings']['reviews-form'] = $oldOptions['settings']['form'];
			unset( $oldOptions['settings']['form'] );
		}

		// migrate "yes/no" settings options
		foreach( ['approval', 'login'] as $option ) {
			$yesno = &$oldOptions['settings']['general']['require'][ $option ];
			$yesno = empty( $yesno ) ? 'no' : 'yes';
		}

		$newOptions = get_option( $this->db->getOptionName(), [] );
		$newOptions = array_replace_recursive( $oldOptions, $newOptions );

		// save migrated plugin options
		update_option( $this->db->getOptionName(), $newOptions );

		// set any new defaults
		$this->db->setDefaults();
	}

	/**
	 * Migrate plugin options
	 *
	 * @return void
	 */
	public function options_230()
	{
		$dateEnabled = $this->db->getOption( 'reviews.date.enabled', false, true );
		if( $dateEnabled === false )return;
		$dateCustom = $this->db->getOption( 'reviews.date.format', '', true );
		$dateFormat = $dateEnabled == 'no'
			? 'default'
			: 'custom';
		$this->db->setOption( 'reviews.date.custom', $dateCustom, true );
		$this->db->setOption( 'reviews.date.format', $dateFormat, true );
		$this->db->deleteOption( 'reviews.date.enabled', true );
	}

	/**
	 * @return void
	 */
	public function reviewSlug_200()
	{
		global $wpdb;

		$query = "UPDATE {$wpdb->posts} " .
		"SET post_name = REPLACE(post_name, 'locallocal', 'local') " .
		"WHERE post_type = '" . App::POST_TYPE . "'";

		$wpdb->query( $query );
	}

	/**
	 * @return void
	 */
	public function reviewType_200()
	{
		global $wpdb;

		$query = "UPDATE {$wpdb->postmeta} AS pm " .
		"INNER JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID " .
		"SET pm.meta_key = 'review_type' " .
		"WHERE pm.meta_key = 'site_name' " .
		"AND pm.meta_value = 'local' " .
		"AND p.post_type = '" . App::POST_TYPE . "'";

		$wpdb->query( $query );
	}

	/**
	 * @return void
	 */
	public function sidebarWidgets_200()
	{
		$sidebarWidgets = get_option( 'sidebars_widgets' );
		$sidebarWidgets = $this->replaceWidgetNames_200( $sidebarWidgets );
		update_option( 'sidebars_widgets', $sidebarWidgets );
	}

	/**
	 * @return void
	 */
	public function themeMods_200()
	{
		global $wpdb;
		$themeMods = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '%theme_mods_%'" );
		foreach( $themeMods as $theme ) {
			$themeMod = get_option( $theme );
			if( !isset( $themeMod['sidebars_widgets']['data'] ))continue;
			$themeMod['sidebars_widgets']['data'] = $this->replaceWidgetNames_200( $themeMod['sidebars_widgets']['data'] );
			update_option( $theme, $themeMod );
		}
	}

	/**
	 * @return void
	 */
	public function translations_230()
	{
		$options = $this->db->getOptions();
		if( !isset( $options['settings']['reviews-form'] ))return;
		$defaults = array_fill_keys( ['rating', 'title', 'content', 'name', 'email', 'terms', 'submit'], [] );
		$reviewsForm = shortcode_atts( $defaults, $options['settings']['reviews-form'] );
		foreach( $reviewsForm as &$option ) {
			$option = wp_parse_args( $option, ['label' => '', 'placeholder' => ''] );
		}
		$strings = [];
		$migrate = array_filter([
			'Your overall rating' => $reviewsForm['rating']['label'],
			'Title of your review' => $reviewsForm['title']['label'],
			'Your review' => $reviewsForm['content']['label'],
			'Your name' => $reviewsForm['name']['label'],
			'Your email' => $reviewsForm['email']['label'],
			'This review is based on my own experience and is my genuine opinion.' => $reviewsForm['terms']['label'],
			'Submit your review' => $reviewsForm['submit']['label'],
			'Summarize your review or highlight an interesting detail' => $reviewsForm['title']['placeholder'],
			'Tell people your review' => $reviewsForm['content']['placeholder'],
			'Tell us your name' => $reviewsForm['name']['placeholder'],
			'Tell us your email' => $reviewsForm['email']['placeholder'],
		]);
		foreach( $migrate as $id => $translation ) {
			$strings[] = [
				'id' => $id,
				's1' => $id,
				's2' => $translation,
			];
		}
		foreach( $defaults as $key => $value ) {
			unset( $options['settings']['reviews-form'][$key] );
		}
		$options['settings']['strings'] = $strings;
		$this->db->setOptions( $options['settings'], true );
	}

	/**
	 * @return void
	 */
	public function widgetSiteReviews_200()
	{
		$oldWidget = get_option( "widget_{$this->app->id}_recent_reviews" );

		if( !$oldWidget )return;

		foreach( $oldWidget as &$widget ) {

			if( !is_array( $widget ))continue;

			$migrate = [
				'excerpt'     => 'title',
				'max_reviews' => 'count',
				'min_rating'  => 'rating',
				'title'       => 'excerpt',
				'type'        => 'display',
			];

			$hide = [];

			if( isset( $widget['show'] )) {
				foreach( ['author','date','rating'] as $value ) {
					if( in_array( 'show_' . $value, $widget['show'] ))continue;
					$hide[] = $value;
				}
			}

			if( isset( $migrate[ $widget['display']] )) {
				$hide[] = $migrate[ $widget['display']];
			}

			$widget['hide']    = $hide;
			$widget['display'] = '';

			foreach( $migrate as $old => $new ) {
				if( isset( $widget[ $old ] ) && !in_array( $old, ['title', 'excerpt'] )) {
					$widget[ $new ] = $widget[ $old ];
				}
			}

			foreach( ['max_reviews','min_rating','order_by','show','type'] as $value ) {
				if( isset( $widget[ $value ] )) {
					unset( $widget[ $value ] );
				}
			}
		}

		update_option( "widget_{$this->app->id}_site-reviews", $oldWidget );
		delete_option( "widget_{$this->app->id}_recent_reviews" );
	}

	/**
	 * @return void
	 */
	public function widgetSiteReviewsForm_200()
	{
		$oldWidget = get_option( "widget_{$this->app->id}_reviews_form" );

		if( !$oldWidget )return;

		foreach( $oldWidget as &$widget ) {

			if( !is_array( $widget ))continue;

			if( isset( $widget['fields'] ) && is_array( $widget['fields'] )) {

				if(( $key = array_search( 'reviewer', $widget['fields'] )) !== false ) {
					$widget['fields'][ $key ] = 'name';
				}

				$widget['hide'] = $widget['fields'];
				unset( $widget['fields'] );
			}
		}

		update_option( "widget_{$this->app->id}_site-reviews-form", $oldWidget );
		delete_option( "widget_{$this->app->id}_reviews_form" );
	}

	public function reviewAssignedTo_210()
	{
		global $wpdb;

		$query = "INSERT INTO gl_postmeta (post_id, meta_key, meta_value) " .
		"SELECT p.ID, 'assigned_to', '' " .
		"FROM gl_posts p " .
		"WHERE NOT EXISTS (" .
			"SELECT pm.post_id " .
			"FROM gl_postmeta pm " .
			"WHERE p.ID = pm.post_id " .
			"AND pm.meta_key = 'assigned_to'" .
		") " .
		"AND p.post_type = 'site-review'";

		$wpdb->query( $query );
	}

	/**
	 * @param string $search
	 * @param string $replace
	 *
	 * @return array
	 */
	protected function replaceWidgetName( $search, $replace, array $widgetNames )
	{
		$search = $this->app->id . $search;
		$replace = $this->app->id . $replace;

		foreach( $widgetNames as $index => $widgetName ) {
			if( strpos( $widgetName, $search ) === false )continue;
			$widgetNames[ $index ] = str_replace( $search, $replace, $widgetName );
		}

		return $widgetNames;
	}

	/**
	 * @return array
	 */
	protected function replaceWidgetNames_200( array $widgets )
	{
		foreach( $widgets as &$values ) {

			if( !is_array( $values ))continue;

			$values = $this->replaceWidgetName( '_recent_reviews', '_site-reviews', $values );
			$values = $this->replaceWidgetName( 'recent_reviews', '_site-reviews', $values );
			$values = $this->replaceWidgetName( '_reviews_form', '_site-reviews-form', $values );
			$values = $this->replaceWidgetName( 'reviews_form', '_site-reviews-form', $values );
		}

		return $widgets;
	}
}
