/* jshint browser:true, globalstrict:true, esversion:6 */
/* global console, site_reviews, StarRating, GLSR, jQuery, Validatinator */

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
	for( var i = 0; i < formEl.length; i++ ) {
		GLSR.clearFieldError( formEl[i] );
	}
};

GLSR.showFormErrors = function( formEl, errors )
{
	var fieldEl;
	var errorsEl;

	for( var error in errors ) {

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

GLSR.submitForm = function( formEl )
{
	var button = formEl.querySelector( 'input[type="submit"]' );
	var data = {
		action : site_reviews.action,
		request: GLSR.parseFormData( formEl ),
	};

	GLSR.postAjax( site_reviews.ajaxurl, data, function( response ) {

		GLSR.clearFormErrors( formEl );
		GLSR.showFormMessage( formEl, response );

		if( button.disabled ) {
			button.removeAttribute( 'disabled' );
		}

		if( !!response.errors ) {
			GLSR.showFormErrors( formEl, response.errors );
		}
		else {
			formEl.reset();
		}
	});
};

GLSR.on( 'change', 'form.glsr-submit-review-form', function( ev )
{
	GLSR.clearFieldError( ev.target );
});

GLSR.on( 'submit', 'form.glsr-submit-review-form', function( ev )
{
	ev.preventDefault();

	var button = this.querySelector( 'input[type="submit"]' );

	if( !button.disabled ) {
		button.setAttribute( 'disabled', '' );
	}

	GLSR.submitForm( this );
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
