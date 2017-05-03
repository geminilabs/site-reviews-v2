/* jshint browser:true, globalstrict:true, esversion:6 */
/* global console, GLSR, grecaptcha, HTMLFormElement, site_reviews, StarRating */

"use strict";

GLSR.activeForm = null;

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

GLSR.searchRecaptchaObject = function( callback )
{
	var result = -1;
	if( window.hasOwnProperty( '___grecaptcha_cfg' )) {
		var clients = window.___grecaptcha_cfg.clients;
		var i, key;
		for( i in clients ) {
			for( key in clients[i] ) {
				if( !( result = callback( clients[i][key], i )))continue;
				return result;
			}
		}
	}
	return result;
};

GLSR.getRecaptchaOverlay = function()
{
	return GLSR.searchRecaptchaObject( function( value ) {
		if( Object.prototype.toString.call( value ) !== '[object Object]' )return;
		for( var obj in value) {
			if( Object.prototype.toString.call( value[obj] ) !== '[object HTMLDivElement]' )continue;
			if( value[obj].className === '' ) {
				return value[obj].firstChild;
			}
		}
	});
};

GLSR.getRecaptchaId = function( formEl )
{
	return GLSR.searchRecaptchaObject( function( value, id ) {
		if( Object.prototype.toString.call( value ) !== '[object HTMLDivElement]' )return;
		if( value.closest( 'form' ) === formEl ) {
			return id;
		}
	});
};

GLSR.executeRecaptcha = function( formEl )
{
	var recaptchaId = GLSR.getRecaptchaId( formEl );
	if( recaptchaId !== -1 ) {
		return grecaptcha.execute( recaptchaId );
	}
	// recaptcha ID not found so pass through an error
	return GLSR.submitForm( false );
};

GLSR.resetRecaptcha = function( formEl )
{
	var recaptchaId = GLSR.getRecaptchaId( formEl );
	if( recaptchaId !== -1 ) {
		grecaptcha.reset( recaptchaId );
	}
};

GLSR.showFormErrors = function( formEl, errors )
{
	if( !errors )return;

	var fieldEl, errorsEl;

	for( var error in errors ) {
		if( !errors.hasOwnProperty( error ))continue;

		fieldEl = formEl.querySelector( '[name="' + error + '"]' ).closest( '.glsr-field' );
		GLSR.addClass( fieldEl, 'glsr-has-error' );

		errorsEl = fieldEl.querySelector( '.glsr-field-errors' );
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

GLSR.buildFormData = function( recaptchaToken )
{
	if( recaptchaToken === undefined ) {
		recaptchaToken = '';
	}
	// console.log( 'recaptchaToken: ' + recaptchaToken );
	return {
		action: site_reviews.action,
		request: GLSR.parseFormData( GLSR.activeForm ),
		'g-recaptcha-response': recaptchaToken,
	};
};

GLSR.submitForm = function( recaptchaToken )
{
	var form = GLSR.activeForm;
	var button = form.querySelector( '[type="submit"]' );

	button.setAttribute( 'disabled', '' );

	GLSR.postAjax( site_reviews.ajaxurl, GLSR.buildFormData( recaptchaToken ), function( response ) {
		// console.log( response );
		if( response.recaptcha ) {
			// console.log( 'executing recaptcha' );
			return GLSR.executeRecaptcha( form );
		}
		if( response.errors === false ) {
			// console.log( 'reseting recaptcha' );
			GLSR.resetRecaptcha( form );
			form.reset();
		}
		// console.log( 'submission finished' );
		GLSR.showFormErrors( form, response.errors );
		GLSR.showFormMessage( form, response );
		GLSR.activeForm = null;

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
	// console.log( 'submitting review' );

	GLSR.activeForm = this;
	GLSR.clearFormErrors( this );
	GLSR.submitForm();
});

/**
 * This event function exists to undo the mayhem caused by the invisible-recaptcha plugin
 * This function is triggered on the invisible-recaptcha callback
 */
GLSR.on( 'click', '.glsr-field [type="submit"]', function()
{
	this.closest( 'form' ).onsubmit = null;
	HTMLFormElement.prototype._submit = HTMLFormElement.prototype.submit;
	HTMLFormElement.prototype.submit = function() {
		if( null === this.querySelector( '.glsr-field' )) {
			this._submit();
		}
		var token = this.querySelector( '#g-recaptcha-response' );
		if( token ) {
			token = token.value;
		}
		GLSR.submitForm( token );
	};
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
