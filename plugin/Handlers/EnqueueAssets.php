<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Commands\EnqueueAssets as Command;

class EnqueueAssets
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $dependencies;

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		$this->dependencies = $this->app->make( 'Html' )->getDependencies();

		if( is_admin() ) {
			$this->enqueueAdmin( $command );
		}
		else {
			$this->enqueuePublic( $command );
		}

		wp_localize_script( $command->handle, 'site_reviews', [
			'action'  => $this->app->prefix . '_action',
			'ajaxurl' => wp_nonce_url( admin_url( 'admin-ajax.php' ), "{$this->app->id}-ajax-nonce" ),
		]);
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

		if( !( $screen->post_type == 'site-review'
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
}
