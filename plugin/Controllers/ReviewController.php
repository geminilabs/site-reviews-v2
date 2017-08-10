<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Commands\SubmitReview;
use GeminiLabs\SiteReviews\Controllers\BaseController;
use GeminiLabs\SiteReviews\Strings;
use WP_Post;
use WP_Screen;

class ReviewController extends BaseController
{
	/**
	 * @return void
	 */
	public function approve()
	{
		check_admin_referer( 'approve-review_' . ( $post_id = $this->getPostId() ));

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
	 * @return void
	 *
	 * @action admin_print_scripts
	 */
	public function modifyAutosave()
	{
		if( $this->isEditReview() && !$this->canEditReview() ) {
			wp_deregister_script( 'autosave' );
		}
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
	 *
	 * @action current_screen
	 */
	public function modifyFeatures( WP_Screen $screen )
	{
		if( $this->canEditReview()
			|| $screen->post_type != App::POST_TYPE
		)return;

		remove_post_type_support( App::POST_TYPE, 'title' );
		remove_post_type_support( App::POST_TYPE, 'editor' );
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
	 *
	 * @filter ngettext
	 */
	public function modifyStatusFilter( $translation, $single, $plural, $number, $domain )
	{
		if( $this->canModifyTranslation( $domain )) {

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
	 *
	 * @filter post_updated_messages
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
			? sprintf( $strings['restored'], wp_post_revision_title( (int) $restored, false ))
			: false;

		$scheduled_date = date_i18n( 'M j, Y @ H:i', strtotime( $post->post_date ));

		$messages[ App::POST_TYPE ] = [
			 1 => $strings['updated'],
			 4 => $strings['updated'],
			 5 => $restored,
			 6 => $strings['published'],
			 7 => $strings['saved'],
			 8 => $strings['submitted'],
			 9 => sprintf( $strings['scheduled'], sprintf( '<strong>%s</strong>', $scheduled_date )),
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
	 *
	 * @filter bulk_post_updated_messages
	 */
	public function modifyUpdateMessagesBulk( array $messages, array $counts )
	{
		$messages[ App::POST_TYPE ] = [
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
	 * @return mixed
	 * @throws Exception
	 */
	public function postSubmitReview( array $request )
	{
		$session = $this->app->make( 'Session' );

		$validatedRequest = $this->validateSubmittedReview( $request );
		if( !is_array( $validatedRequest )) {
			return __( 'Please fix the submission errors.', 'site-reviews' );
		}

		if( !empty( $request['gotcha'] )) {
			glsr_resolve( 'Log\Logger' )->warning( 'The Honeypot caught a bad submission:' );
			glsr_resolve( 'Log\Logger' )->warning( $request );
			return __( 'The review submission failed. Please notify the site administrator.', 'site-reviews' );
		}

		$customValidation = apply_filters( 'site-reviews/validate/review/submission', true, $validatedRequest );
		if( $customValidation !== true ) {
			$session->set( "{$validatedRequest['form_id']}-errors", [] );
			$session->set( "{$validatedRequest['form_id']}-values", $validatedRequest );
			return is_string( $customValidation )
				? $customValidation
				: __( 'The review submission failed. Please notify the site administrator.', 'site-reviews' );
		}

		$validateRecaptcha = $this->validateRecaptcha();

		// recaptcha response was empty so it hasn't been set yet
		if( is_null( $validateRecaptcha )) {
			$session->set( "{$request['form_id']}-recaptcha", true );
			return;
		}

		if( !$validateRecaptcha ) {
			$session->set( "{$request['form_id']}-errors", [] );
			$session->set( "{$request['form_id']}-recaptcha", 'reset' );
			return __( 'The reCAPTCHA verification failed. Please notify the site administrator.', 'site-reviews' );
		}

		return $this->execute( new SubmitReview( $validatedRequest ));
	}

	/**
	 * @return void
	 *
	 * @action admin_menu
	 */
	public function removeMetaBoxes()
	{
		remove_meta_box( 'slugdiv', App::POST_TYPE, 'advanced' );
	}

	/**
	 * @return void
	 */
	public function revert()
	{
		check_admin_referer( 'revert-review_' . ( $post_id = $this->getPostId() ));

		$this->db->revertReview( $post_id );

		$this->redirect( $post_id, 52 );
	}

	/**
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function saveAssignedToMetabox( $post_id )
	{
		if( !wp_verify_nonce( filter_input( INPUT_POST, '_nonce-assigned-to' ), 'assigned_to' ))return;
		$assignedTo = filter_input( INPUT_POST, 'assigned_to' );
		$assignedTo || $assignedTo = '';
		update_post_meta( $post_id, 'assigned_to', $assignedTo );
	}

	/**
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function saveEditedReview( $post_id )
	{
		$this->saveAssignedToMetabox( $post_id );
		$this->saveResponseMetabox( $post_id );
	}

	/**
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function saveResponseMetabox( $post_id )
	{
		if( !wp_verify_nonce( filter_input( INPUT_POST, '_nonce-response' ), 'response' ))return;
		$response = filter_input( INPUT_POST, 'response' );
		$response || $response = '';
		update_post_meta( $post_id, 'response', trim( wp_kses( $response, [
			'a' => ['href' => [], 'title' => []],
			'em' => [],
			'strong' => [],
		])));
	}

	/**
	 * Set/persist custom permissions for the post_type
	 *
	 * @return void
	 */
	public function setPermissions()
	{
		foreach( wp_roles()->roles as $role => $value ) {
			wp_roles()->remove_cap( $role, sprintf( 'create_%s', App::POST_TYPE ));
		}
	}

	/**
	 * @return void
	 */
	public function unapprove()
	{
		check_admin_referer( 'unapprove-review_' . ( $post_id = $this->getPostId() ));

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
		$postId = filter_input( INPUT_GET, 'post' );

		$reviewType = get_post_meta( $postId, 'review_type', true );

		return $postId > 0
			&& $reviewType == 'local'
			&& $this->isEditReview();
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
			&& $current_screen->post_type == App::POST_TYPE
			&& $domain == 'default';
	}

	/**
	 * @return bool
	 */
	protected function isEditReview()
	{
		$screen = glsr_current_screen();

		return $screen
			&& $screen->base == 'post'
			&& $screen->id == App::POST_TYPE
			&& $screen->post_type == App::POST_TYPE;
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
			? add_query_arg( ['message' => $message_index ], get_edit_post_link( $post_id, false ))
			: add_query_arg( ['message' => $message_index ], remove_query_arg( ['trashed', 'untrashed', 'deleted', 'ids'], $referer ));

		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * @return bool
	 */
	protected function validateCustomRecaptcha( $recaptchaResponse )
	{
		$response = json_decode( wp_remote_retrieve_body( wp_remote_get( add_query_arg([
			'remoteip' => $this->app->make( 'Helper' )->getIpAddress(),
			'response' => $recaptchaResponse,
			'secret' => glsr_get_option( 'reviews-form.recaptcha.secret' ),
		], 'https://www.google.com/recaptcha/api/siteverify' ))));

		if( !empty( $response->success )) {
			return $response->success;
		}
		$errorCodes = [
			'missing-input-secret'   => 'The secret parameter is missing.',
			'invalid-input-secret'   => 'The secret parameter is invalid or malformed.',
			'missing-input-response' => 'The response parameter is missing.',
			'invalid-input-response' => 'The response parameter is invalid or malformed.',
			'bad-request'            => 'The request is invalid or malformed.',
		];
		foreach( $response->{'error-codes'} as $error ) {
			glsr_resolve( 'Log\Logger' )->error( sprintf( 'reCAPTCHA: %s', $errorCodes[$error] ));
		}
		return false;
	}

	/**
	 * @return bool
	 */
	protected function validateRecaptcha()
	{
		$integration = glsr_get_option( 'reviews-form.recaptcha.integration' );
		$recaptchaResponse = filter_input( INPUT_POST, 'g-recaptcha-response' );

		if( !$integration ) {
			return true;
		}
		// if response is empty we need to return null
		if( empty( $recaptchaResponse ))return;

		if( $integration == 'custom' ) {
			return $this->validateCustomRecaptcha( $recaptchaResponse );
		}
		if( $integration == 'invisible-recaptcha' ) {
			// if plugin is inactive, return true
			return apply_filters( 'google_invre_is_valid_request_filter', true );
		}
		return false;
	}

	/**
	 * @return false|array
	 */
	protected function validateSubmittedReview( array $request )
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
		$rules = array_diff_key( $rules, array_flip( $excluded ));

		$user = wp_get_current_user();

		$defaults = [
			'assign_to' => '',
			'category'  => '',
			'content'   => '',
			'email'     => ( $user->exists() ? $user->user_email : '' ),
			'form_id'   => '',
			'name'      => ( $user->exists() ? $user->display_name : __( 'Anonymous', 'site-reviews' )),
			'rating'    => '',
			'terms'     => '',
			'title'     => __( 'No Title', 'site-reviews' ),
		];

		if( !$this->validate( $request, $rules )) {
			return false;
		}

		return array_merge( $defaults, $request );
	}
}
