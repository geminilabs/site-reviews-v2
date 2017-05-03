/* jshint browser:true, globalstrict:true, esversion:6 */
/* global console, grecaptcha, site_reviews, StarRating, GLSR, jQuery, Validatinator */

"use strict";

GLSR.clearFieldError = function( el )
{
	var fieldEl = el.closest( '.glsr-field' );

	if( fieldEl === null )return;

	var errorEl = fieldEl.querySelector( '.glsr-field-errors' );

	GLSR.removeClass( fieldEl, 'glsr-has-error' );

	if( errorEl !== null ) {
		errorEl.parentNode.removeChild( errorEl );
	}
};

GLSR.clearFormErrors = function( formEl )
{
	GLSR.clearFormMessages( formEl );
	for( var i = 0; i < formEl.length; i++ ) {
		GLSR.clearFieldError( formEl[i] );
	}
};

GLSR.clearFormMessages = function( formEl )
{
	var messageEl = formEl.querySelector( '.glsr-form-messages' );
	if( messageEl ) {
		messageEl.innerHTML = '';
	}
};

GLSR.getRecaptchaId = function( formEl )
{
	var recaptchaEl = formEl.querySelector( '.glsr-recaptcha-holder' );
	return recaptchaEl ? recaptchaEl.getAttribute( 'data-id' ) : -1;
};

GLSR.showFormErrors = function( formEl, errors )
{
	var fieldEl;
	var errorsEl;

	for( var error in errors ) {

		if( !errors.hasOwnProperty( error ) )continue;

		fieldEl = formEl.querySelector( '[name="' + error + '"]' ).closest( '.glsr-field' );
		errorsEl = fieldEl.querySelector( '.glsr-field-errors' );

		GLSR.addClass( fieldEl, 'glsr-has-error' );

		if( errorsEl === null ) {
			errorsEl = GLSR.appendTo( fieldEl, 'span', {
				'class': 'glsr-field-errors',
			});
		}

		for( var i = 0; i < errors[ error ].errors.length; i++ ) {
			if( errors[ error ].errors[i] === null )continue;
			errorsEl.innerHTML += '<span class="glsr-field-error">' + errors[ error ].errors[i] + '</span>';
		}
	}
};

GLSR.showFormMessage = function( formEl, response )
{
	var formIdEl  = formEl.querySelector( 'input[name="form_id"]' );
	var messageEl = formEl.querySelector( '.glsr-form-messages' );

	if( messageEl === null ) {
		messageEl = GLSR.insertAfter( formIdEl, 'div', {
			'class': 'glsr-form-messages',
		});
	}

	if( !!response.errors ) {
		GLSR.addClass( messageEl, 'gslr-has-errors' );
	}
	else {
		GLSR.removeClass( messageEl, 'gslr-has-errors' );
	}

	messageEl.innerHTML = '<p>' + response.message + '</p>';
};

GLSR.submitForm = function( recaptchaToken )
{
	var form = GLSR.activeForm;
	var button = form.querySelector( '[type="submit"]' );

	button.setAttribute( 'disabled', '' );

	if( recaptchaToken ) {
		GLSR.activeData.request['g-recaptcha-response'] = recaptchaToken;
		console.log( GLSR.activeData.request );
	}
	GLSR.postAjax( site_reviews.ajaxurl, GLSR.activeData, function( response ) {
		GLSR.activeForm = null;
		GLSR.activeData = null;
		GLSR.showFormMessage( form, response );
		button.removeAttribute( 'disabled' );
		if( response.errors !== false ) {
			return GLSR.showFormErrors( form, response.errors );
		}
		form.reset();
	});
};

GLSR.validateForm = function()
{
	var form = GLSR.activeForm;
	var button = form.querySelector( '[type="submit"]' );

	button.setAttribute( 'disabled', '' );

	GLSR.activeData = {
		action : site_reviews.action,
		request: GLSR.parseFormData( form ),
	};
	GLSR.postAjax( site_reviews.ajaxurl, GLSR.activeData, function( response ) {
		if( response.errors === false ) {
			var recaptchaId = GLSR.getRecaptchaId( form );
			if( recaptchaId !== -1 ) {
				button.removeAttribute( 'disabled' );
				return grecaptcha.execute( recaptchaId );
			}
			return GLSR.submitForm();
		}
		GLSR.showFormErrors( form, response.errors );
		GLSR.showFormMessage( form, response );
		button.removeAttribute( 'disabled' );
	});
};

GLSR.on( 'change', 'form.glsr-submit-review-form', function( ev )
{
	GLSR.clearFieldError( ev.target );
});

GLSR.on( 'submit', 'form.glsr-submit-review-form', function( ev )
{
	ev.preventDefault();

	GLSR.activeForm = this;
	GLSR.clearFormErrors( this );
	GLSR.validateForm();
});

GLSR.ready( function()
{
	var ratings = document.querySelectorAll( 'select.glsr-star-rating' );

	for( var i = 0; i < ratings.length; i++ ) {
		new StarRating( ratings[i], {
			clearable: false,
			showText : false,
			onClick  : GLSR.clearFieldError,
		});
	}
});
