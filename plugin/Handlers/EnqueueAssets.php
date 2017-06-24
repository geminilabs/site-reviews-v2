<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\App;
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
		$ajaxNonce = wp_create_nonce( glsr_app()->id . '-ajax-nonce' );
		$variables = [
			'action'  => glsr_app()->prefix . '_action',
			'ajaxurl' => add_query_arg( '_nonce', $ajaxNonce, admin_url( 'admin-ajax.php' )),
			'ajaxnonce' => $ajaxNonce,
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
		$screen = glsr_current_screen();

		$dependencies = array_merge( $this->dependencies, ['jquery', 'jquery-ui-sortable', 'underscore', 'wp-util'] );

		wp_enqueue_style(
			$command->handle,
			$command->url . 'css/site-reviews-admin.css',
			[],
			$command->version
		);

		if( !$screen || !( $screen->post_type == App::POST_TYPE
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
		$currentTheme = sanitize_title( wp_get_theme()->get( 'Name' ));

		$stylesheet = file_exists( $command->path . "css/{$currentTheme}.css" )
			? $command->url . "css/{$currentTheme}.css"
			: $command->url . 'css/site-reviews.css';

		if( apply_filters( 'site-reviews/assets/css', true )) {
			wp_enqueue_style(
				$command->handle,
				$stylesheet,
				[],
				$command->version
			);
		}

		if( glsr_get_option( 'reviews-form.recaptcha.integration' ) == 'custom' ) {
			$this->enqueueRecaptchaScript( $command );
		}

		if( !apply_filters( 'site-reviews/assets/js', true ))return;

		wp_enqueue_script(
			$command->handle,
			$command->url . 'js/site-reviews.js',
			['jquery'],
			$command->version,
			true
		);
	}

	/**
	 * Enqueue custom integration reCAPTCHA script
	 *
	 * @return void
	 */
	public function enqueueRecaptchaScript( Command $command )
	{
		wp_enqueue_script( $command->handle . '/google-recaptcha', add_query_arg([
			'hl' => apply_filters( 'site-reviews/recaptcha/language', get_locale() ),
			'onload' => 'glsr_render_recaptcha',
			'render' => 'explicit',
		], 'https://www.google.com/recaptcha/api.js' ));

		$inlineScript = file_get_contents( sprintf( '%sjs/recaptcha.js', $command->path ));

		wp_add_inline_script( $command->handle . '/google-recaptcha', $inlineScript, 'before' );
	}

	/**
	 * Enqueue TinyMCE plugins
	 *
	 * @return array|null
	 */
	public function enqueueTinymcePlugins( array $plugins )
	{
		if( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ))return;

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
			if( !empty( $args['required'] )) {
				$variables[ $tag ] = $args['required'];
			}
		}

		return $variables;
	}
}
