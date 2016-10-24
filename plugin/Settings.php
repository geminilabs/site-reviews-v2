<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use ReflectionClass;
use ReflectionMethod;

class Settings
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Html
	 */
	protected $html;

	/**
	 * @var array
	 */
	protected $settings;

	public function __construct( App $app )
	{
		$this->app      = $app;
		$this->html     = $app->make( 'Html' );
		$this->settings = [];
	}

	/**
	 * Add a setting default
	 *
	 * @param string $formId
	 *
	 * @return void
	 */
	public function addSetting( $formId, array $args )
	{
		if( isset( $args['name'] ) ) {
			$this->settings[ $args['name'] ] = $this->getDefault( $args );
		}

		$this->html->addfield( $formId, $args );
	}

	/**
	 * Get the default field value
	 *
	 * @return string
	 */
	public function getDefault( array $args )
	{
		isset( $args['default'] ) ?: $args['default'] = '';
		isset( $args['placeholder'] ) ?: $args['placeholder'] = '';

		if( $args['default'] === ':placeholder' ) {
			$args['default'] = $args['placeholder'];
		}

		return $args['default'];
	}

	/**
	 * Get the default settings
	 *
	 * @return array
	 */
	public function getSettings()
	{
		$this->register();

		return $this->settings;
	}

	/**
	 * Register the settings for each form
	 *
	 * @return void
	 *
	 * @action admin_init
	 */
	public function register()
	{
		if( !empty( $this->settings ) )return;

		$methods = (new ReflectionClass( __CLASS__ ))->getMethods( ReflectionMethod::IS_PROTECTED );

		foreach( $methods as $method ) {
			if( substr( $method->name, 0, 3 ) === 'set' ) {
				$this->{$method->name}();
			}
		}
	}

	protected function setGeneral()
	{
		$formId = 'settings/general';

		$this->html->createForm( $formId, [
			'action' => admin_url( 'options.php' ),
			'nonce'  => $this->app->id . '-settings',
			'submit' => __( 'Save Settings', 'geminilabs-site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'yesno_inline',
			'name'    => 'general.require.approval',
			'label'   => __( 'Require approval', 'geminilabs-site-reviews' ),
			'default' => true,
			'desc'    => __( 'Set the status of new review submissions to pending.', 'geminilabs-site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'yesno_inline',
			'name'  => 'general.require.login',
			'label' => __( 'Require login', 'geminilabs-site-reviews' ),
			'desc'  => __( 'Only allow review submissions from registered users.', 'geminilabs-site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'radio',
			'name'    => 'general.notification',
			'label'   => __( 'Notifications', 'geminilabs-site-reviews' ),
			'default' => 'none',
			'options' => [
				'none'    => __( 'Do not send review notifications', 'geminilabs-site-reviews' ),
				'default' => sprintf( __( 'Send to administrator <code>%s</code>', 'geminilabs-site-reviews' ), get_option( 'admin_email' ) ),
				'custom'  => __( 'Send to one or more custom emails', 'geminilabs-site-reviews' ),
			],
		]);

		$this->addSetting( $formId, [
			'type'    => 'text',
			'name'    => 'general.notification_email',
			'label'   => __( 'Send notification emails to', 'geminilabs-site-reviews' ),
			'depends' => [
				'general.notification' => 'custom',
			],
			'placeholder' => __( 'Separate multiple emails with a comma', 'geminilabs-site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'code',
			'name'    => 'general.notification_message',
			'label'   => __( 'Notification template', 'geminilabs-site-reviews' ),
			'rows'    => 9,
			'depends' => [
				'general.notification' => ['custom', 'default'],
			],
			'default' => $this->html->renderTemplate( 'email/templates/review-notification', [], 'return' ),
			'desc' => 'To restore the default text, save an empty template. Available template tags:<br>
				<code>{review_rating}</code> - The review rating number (1-5)<br>
				<code>{review_title}</code> - The review title<br>
				<code>{review_content}</code> - The review content<br>
				<code>{review_author}</code> - The review author<br>
				<code>{review_email}</code> - The email of the review author<br>
				<code>{review_ip}</code> - The IP address of the review author<br>
				<code>{review_link}</code> - The link to edit/view a review',
		]);
	}

	protected function setForm()
	{
		$formId = 'settings/form';

		$this->html->createForm( $formId, [
			'action' => admin_url( 'options.php' ),
			'nonce'  => $this->app->id . '-settings',
			'submit' => __( 'Save Settings', 'geminilabs-site-reviews' ),
		]);

		$this->html->addfield( $formId, [
			'type'  => 'heading',
			'value' => __( 'Form Labels', 'geminilabs-site-reviews' ),
			'desc'  => __( 'Customize the label text for the review submission form fields.', 'geminilabs-site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'form.rating.label',
			'label' => __( 'Rating label', 'geminilabs-site-reviews' ),
			'placeholder' => __( 'Your overall rating', 'geminilabs-site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'form.title.label',
			'label' => __( 'Title label', 'geminilabs-site-reviews' ),
			'placeholder' => __( 'Title of your review', 'geminilabs-site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'form.content.label',
			'label' => __( 'Content label', 'geminilabs-site-reviews' ),
			'placeholder' => __( 'Your review', 'geminilabs-site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'form.reviewer.label',
			'label' => __( 'Reviewer label', 'geminilabs-site-reviews' ),
			'placeholder' => __( 'Your name', 'geminilabs-site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'form.email.label',
			'label' => __( 'Email label', 'geminilabs-site-reviews' ),
			'placeholder' => __( 'Your email', 'geminilabs-site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'textarea',
			'name'  => 'form.terms.label',
			'label' => __( 'Terms label', 'geminilabs-site-reviews' ),
			'placeholder' => __( 'This review is based on my own experience and is my genuine opinion.', 'geminilabs-site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->html->addfield( $formId, [
			'type'  => 'heading',
			'value' => __( 'Form Placeholders', 'geminilabs-site-reviews' ),
			'desc'  => __( 'Customize the placeholder text for the review submission form fields. Use a single space character to disable the placeholder text.', 'geminilabs-site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'form.title.placeholder',
			'class' => 'large-text',
			'label' => __( 'Title placeholder', 'geminilabs-site-reviews' ),
			'placeholder' => __( 'Summarize your review or highlight an interesting detail', 'geminilabs-site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'form.content.placeholder',
			'class' => 'large-text',
			'label' => __( 'Content placeholder', 'geminilabs-site-reviews' ),
			'placeholder' => __( 'Tell people your review', 'geminilabs-site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'form.reviewer.placeholder',
			'class' => 'large-text',
			'label' => __( 'Reviewer placeholder', 'geminilabs-site-reviews' ),
			'placeholder' => __( 'Tell us your name', 'geminilabs-site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'form.email.placeholder',
			'class' => 'large-text',
			'label' => __( 'Email placeholder', 'geminilabs-site-reviews' ),
			'placeholder' => __( 'Tell us your email', 'geminilabs-site-reviews' ),
			'default' => ':placeholder',
		]);
	}
}
