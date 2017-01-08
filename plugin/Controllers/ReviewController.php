<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\SubmitReview;
use GeminiLabs\SiteReviews\Controllers\BaseController;
use GeminiLabs\SiteReviews\Strings;

class ReviewController extends BaseController
{
	/**
	 * @return void
	 */
	public function approve()
	{
		check_admin_referer( 'approve-review_' . ( $post_id = $this->getPostId() ) );

		wp_update_post([
			'ID'          => $post_id,
			'post_status' => 'publish',
		]);

		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Remove the autosave functionality
	 *
	 * @return null|void
	 *
	 * @action admin_print_scripts-post.php
	 */
	public function modifyAutosave()
	{
		if( $this->canEditReview() )return;

		wp_deregister_script( 'autosave' );
	}

	/**
	 * Modifies the WP_Editor settings
	 *
	 * @return array
	 *
	 * @action wp_editor_settings
	 */
	public function modifyEditor( array $settings )
	{
		if( $this->canEditReview() ) {
			$settings = [
				'textarea_rows' => 12,
				'media_buttons' => false,
				'quicktags'     => false,
				'tinymce'       => false,
			];
		}

		return $settings;
	}

	/**
	 * Modify the WP_Editor html to allow autosizing without breaking the `editor-expand` script
	 *
	 * @param string $html
	 *
	 * @return string
	 *
	 * @action the_editor
	 */
	public function modifyEditorTextarea( $html )
	{
		if( $this->canEditReview() ) {
			$html = str_replace( '<textarea', '<div id="ed_toolbar"></div><textarea', $html );
		}

		return $html;
	}

	/**
	 * Remove post_type support for all non-local reviews
	 *
	 * @todo: Move this to addons
	 *
	 * @return void
	 */
	public function modifyFeatures()
	{
		if( $this->canEditReview() )return;

		remove_post_type_support( $this->app->post_type, 'title' );
		remove_post_type_support( $this->app->post_type, 'editor' );
	}

	/**
	 * Customize the post_type status text
	 *
	 * @param string $translation
	 * @param string $single
	 * @param string $plural
	 * @param int    $number
	 * @param string $domain
	 *
	 * @return string
	 */
	public function modifyStatusFilter( $translation, $single, $plural, $number, $domain )
	{
		if( $this->canModifyTranslation( $domain ) ) {

			$search = [
				'Published',
				'Pending',
			];

			$replace = [
				__( 'Approved', 'site-reviews' ),
				__( 'Unapproved', 'site-reviews' ),
			];

			foreach( $search as $string ) {
				if( strpos( $single, $string ) === false )continue;

				$translation = $this->getTranslation([
					'number' => $number,
					'plural' => str_replace( $search, $replace, $plural ),
					'single' => str_replace( $search, $replace, $single ),
				]);
			}
		}

		return $translation;
	}

	/**
	 * Customize the updated messages array for this post_type
	 *
	 * @return array
	 */
	public function modifyUpdateMessages( array $messages )
	{
		global $post;

		if( !isset( $post->ID ) || !$post->ID ) {
			return $messages;
		}

		$strings = glsr_resolve( 'Strings' )->post_updated_messages();

		$restored = filter_input( INPUT_GET, 'revision' );
		$restored = $restored
			? sprintf( $strings['restored'], wp_post_revision_title( (int) $restored, false ) )
			: false;

		$scheduled_date = date_i18n( 'M j, Y @ H:i', strtotime( $post->post_date ) );

		$messages[ $this->app->post_type ] = [
			 1 => $strings['updated'],
			 4 => $strings['updated'],
			 5 => $restored,
			 6 => $strings['published'],
			 7 => $strings['saved'],
			 8 => $strings['submitted'],
			 9 => sprintf( $strings['scheduled'], sprintf( '<strong>%s</strong>', $scheduled_date ) ),
			10 => $strings['draft_updated'],
			50 => $strings['approved'],
			51 => $strings['unapproved'],
			52 => $strings['reverted'],
		];

		return $messages;
	}

	/**
	 * Customize the bulk updated messages array for this post_type
	 *
	 * @return array
	 */
	public function modifyUpdateMessagesBulk( array $messages, array $counts )
	{
		$messages[ $this->app->post_type ] = [
			'updated'   => _n( '%s review updated.', '%s reviews updated.', $counts['updated'], 'site-reviews' ),
			'locked'    => _n( '%s review not updated, somebody is editing it.', '%s reviews not updated, somebody is editing them.', $counts['locked'], 'site-reviews' ),
			'deleted'   => _n( '%s review permanently deleted.', '%s reviews permanently deleted.', $counts['deleted'], 'site-reviews' ),
			'trashed'   => _n( '%s review moved to the Trash.', '%s reviews moved to the Trash.', $counts['trashed'], 'site-reviews' ),
			'untrashed' => _n( '%s review restored from the Trash.', '%s reviews restored from the Trash.', $counts['untrashed'], 'site-reviews' ),
		];

		return $messages;
	}

	/**
	 * Submit the review form
	 *
	 * @return void
	 * @throws Exception
	 */
	public function postSubmitReview( array $request )
	{
		$minContentLength = apply_filters( 'site-reviews/local/review/content/minLength', '0' );

		$rules = [
			'content' => 'required|min:' . $minContentLength,
			'email'   => 'required|email|min:5',
			'name'    => 'required',
			'rating'  => 'required|numeric|between:1,5',
			'terms'   => 'accepted',
			'title'   => 'required',
		];

		$excluded = isset( $request['excluded'] )
			? json_decode( $request['excluded'] )
			: [];

		// only use the rules for non-excluded values
		$rules = array_diff_key( $rules, array_flip( $excluded ) );

		$user = wp_get_current_user();

		$defaults = [
			'category' => '',
			'content'  => '',
			'email'    => ( $user->exists() ? $user->user_email : '' ),
			'form_id'  => '',
			'name'     => ( $user->exists() ? $user->display_name : __( 'Anonymous', 'site-reviews' ) ),
			'rating'   => '',
			'terms'    => '',
			'title'    => __( 'No Title', 'site-reviews' ),
		];

		if( !$this->validate( $request, $rules ) ) {
			return __( 'Please fix the submission errors.', 'site-reviews' );
		}

		// normalize the request array
		$request = array_merge( $defaults, $request );

		return $this->execute( new SubmitReview( $request ) );
	}

	/**
	 * @return void
	 *
	 * @action admin_menu
	 */
	public function removeMetaBoxes()
	{
		remove_meta_box( 'slugdiv', $this->app->post_type, 'advanced' );
	}

	/**
	 * @return void
	 */
	public function revert()
	{
		check_admin_referer( 'revert-review_' . ( $post_id = $this->getPostId() ) );

		$this->db->revertReview( $post_id );

		$this->redirect( $post_id, 52 );
	}

	/**
	 * Set/persist custom permissions for the post_type
	 *
	 * @return void
	 */
	public function setPermissions()
	{
		foreach( wp_roles()->roles as $role => $value ) {
			wp_roles()->remove_cap( $role, 'create_reviews' );
		}
	}

	/**
	 * @return void
	 */
	public function unapprove()
	{
		check_admin_referer( 'unapprove-review_' . ( $post_id = $this->getPostId() ) );

		wp_update_post([
			'ID'          => $post_id,
			'post_status' => 'pending',
		]);

		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * @return bool
	 */
	protected function canEditReview()
	{
		$screen = get_current_screen();

		$action = filter_input( INPUT_GET, 'action' );
		$postId = filter_input( INPUT_GET, 'post' );

		if( $action != 'edit'
			|| $postId < 1
			|| $screen->base != 'post'
			|| $screen->post_type != $this->app->post_type ) {
			return false;
		}

		$type = get_post_meta( $postId, 'type', true );

		return 'local' === $type;
	}

	/**
	 * Check if the translation string can be modified
	 *
	 * @param string $domain
	 *
	 * @return bool
	 */
	protected function canModifyTranslation( $domain = 'default' )
	{
		global $current_screen;

		return isset( $current_screen )
			&& $current_screen->base == 'edit'
			&& $current_screen->post_type == $this->app->post_type
			&& $domain == 'default';
	}

	/**
	 * @return int
	 */
	protected function getPostId()
	{
		return (int) filter_input( INPUT_GET, 'post' );
	}

	/**
	 * Get the modified translation string
	 *
	 * @return string
	 */
	protected function getTranslation( array $args )
	{
		$defaults = [
			'number' => 0,
			'plural' => '',
			'single' => '',
			'text'   => '',
		];

		$args = (object) wp_parse_args( $args, $defaults );

		$translations = get_translations_for_domain( 'site-reviews' );

		return $args->text
			? $translations->translate( $args->text )
			: $translations->translate_plural( $args->single, $args->plural, $args->number );
	}

	/**
	 * @param int $post_id
	 * @param int $message_index
	 *
	 * @return void
	 */
	protected function redirect( $post_id, $message_index )
	{
		$referer = wp_get_referer();

		$hasReferer = !$referer
			|| strpos( $referer, 'post.php' ) !== false
			|| strpos( $referer, 'post-new.php' ) !== false;


		$redirect = !$hasReferer
			? add_query_arg( ['message' => $message_index ], get_edit_post_link( $post_id, false ) )
			: add_query_arg( ['message' => $message_index ], remove_query_arg( ['trashed', 'untrashed', 'deleted', 'ids'], $referer ) );

		wp_safe_redirect( $redirect );
		exit();
	}
}
