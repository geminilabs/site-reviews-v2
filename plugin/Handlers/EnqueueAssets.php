<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\EnqueueAssets as Command;

class EnqueueAssets
{
	/**
	 * @var array
	 */
	protected $dependencies;

	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		$this->dependencies = glsr_resolve( 'Html' )->getDependencies();

		$variables = [
			'action'  => glsr_app()->prefix . '_action',
			'ajaxurl' => wp_nonce_url( admin_url( 'admin-ajax.php' ), glsr_app()->id . '-ajax-nonce' ),
		];

		if( is_admin() ) {
			$this->enqueueAdmin( $command );

			if( user_can_richedit() ) {

				add_filter( 'mce_external_plugins', [ $this, 'enqueueTinymcePlugins'], 15 );

				$variables = array_merge( $variables, [
					'shortcodes' => $this->localizeShortcodes(),
				]);
			}
		}
		else {
			$this->enqueuePublic( $command );
		}

		wp_localize_script( $command->handle, 'site_reviews', $variables );
	}

	/**
	 * Enqueue admin assets
	 *
	 * @return void
	 */
	public function enqueueAdmin( Command $command )
	{
		$screen = get_current_screen();

		$dependencies = array_merge( $this->dependencies, ['jquery'] );

		wp_enqueue_style(
			$command->handle,
			$command->url . 'css/site-reviews-admin.css',
			[],
			$command->version
		);

		if( !( $screen->post_type == glsr_app()->post_type
			|| $screen->base == 'post'
			|| $screen->id == 'dashboard'
			|| $screen->id == 'widgets'
		))return;

		wp_enqueue_script(
			$command->handle,
			$command->url . 'js/site-reviews-admin.js',
			$dependencies,
			$command->version,
			true
		);
	}

	/**
	 * Enqueue public assets
	 *
	 * @return void
	 */
	public function enqueuePublic( Command $command )
	{
		$currentTheme = sanitize_title( wp_get_theme()->get( 'Name' ) );

		$stylesheet = file_exists( $command->path . "css/{$currentTheme}.css" )
			? $command->url . "css/{$currentTheme}.css"
			: $command->url . 'css/site-reviews.css';

		if( apply_filters( 'site-reviews/assets/css', true ) ) {
			wp_enqueue_style(
				$command->handle,
				$stylesheet,
				[],
				$command->version
			);
		}

		if( !apply_filters( 'site-reviews/assets/js', true ) )return;

		wp_enqueue_script(
			$command->handle,
			$command->url . 'js/site-reviews.js',
			['jquery'],
			$command->version,
			true
		);
	}

	/**
	 * Enqueue TinyMCE plugins
	 *
	 * @return array|null
	 */
	public function enqueueTinymcePlugins( array $plugins )
	{
		if( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) )return;

		$plugins['glsr_shortcode'] = glsr_app()->url . 'assets/js/mce-plugin.js';

		return $plugins;
	}

	/**
	 * @return array
	 */
	protected function localizeShortcodes()
	{
		$variables = [];

		foreach( glsr_app()->mceShortcodes as $tag => $args ) {
			if( !empty( $args['required'] ) ) {
				$variables[ $tag ] = $args['required'];
			}
		}

		return $variables;
	}
}
