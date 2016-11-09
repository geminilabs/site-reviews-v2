/* jshint browser:true, globalstrict:true, esversion:6 */
/* global ajaxurl, console, jQuery, site_reviews, site_reviews_pointers */

"use strict";

var x = jQuery.noConflict();

var GLSR = {
	addons: {},
	pinned: {},
};

GLSR.colorControls = function()
{
	if( typeof x.wp !== 'object' || typeof x.wp.wpColorPicker !== 'function' )return;

	x( document ).find( 'input[type="text"].color-picker-hex' ).each( function() {
		var t = x( this );
		var options = t.data( 'colorpicker' ) || {};
		t.wpColorPicker( options );
	});
};

GLSR.dismissNotices = function()
{
	x( '.notice.is-dismissible' ).each( function() {
		var notice = x( this );
		notice.fadeTo( 100, 0, function() {
			notice.slideUp( 100, function() {
				notice.remove();
			});
		});
	});
};

GLSR.getURLParameter = function( name )
{
	return decodeURIComponent(
		(new RegExp( '[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)' ).exec( location.search ) || [null, ''])[1].replace( /\+/g, '%20' )
	) || null;
};

GLSR.insertNotices = function( notices )
{
	notices = notices || false;

	if( !notices )return;

	if( !x( '#glsr-notices' ).length ) {
		x( '#message.notice' ).remove();
		x( 'form#post' ).before( '<div id="glsr-notices" />' );
	}

	x( '#glsr-notices' ).html( notices );

	x( document ).trigger( 'wp-updates-notice-added' );
};

GLSR.normalizeValue = function( value )
{
	if(['true','on','1'].indexOf( value ) > -1 ) {
		return true;
	}

	if(['false','off','0'].indexOf( value ) > -1 ) {
		return false;
	}

	return value;
};

GLSR.normalizeValues = function( array )
{
	return array.map( GLSR.normalizeValue );
};

GLSR.onClearLog = function( e )
{
	e.preventDefault();

	var el = x( this );

	if( el.is( ':disabled' ) )return;

	var data = {
		action: site_reviews.action,
		request: {
			action: 'clear-log',
		},
	};

	el.prop( 'disabled', true );

	x.post( site_reviews.ajaxurl, data, function( response )
	{
		GLSR.insertNotices( response.notices );

		x( '#log-file' ).val( response.log );

		el.prop( 'disabled', false );

	}, 'json' );
};

GLSR.onFieldChange = function()
{
	var depends = x( this ).closest( 'form' ).find( '[data-depends]' );

	if( !depends.length )return;

	var name  = this.getAttribute( 'name' );
	var type  = this.getAttribute( 'type' );

	for( var i = 0; i < depends.length; i++ ) {

		try {
			var data = JSON.parse( depends[i].getAttribute( 'data-depends' ) );
			var bool;

			if( data.name !== name )continue;

			if( 'checkbox' === type ) {
				bool = !!this.checked;
			}
			else if( x.isArray( data.value ) ) {
				bool = x.inArray( GLSR.normalizeValue( this.value ), GLSR.normalizeValues( data.value ) ) !== -1;
			}
			else {
				bool = GLSR.normalizeValue( data.value ) === GLSR.normalizeValue( this.value );
			}

			GLSR.toggleHiddenField( depends[i], bool );
		}
		catch( e ) {
			console.error( 'JSON Error: ' + depends[i] );
		}
	}
};

GLSR.pointers = function( pointer )
{
	x( pointer.target ).pointer({
		content: pointer.options.content,
		position: pointer.options.position,
		close: function() {
			x.post( ajaxurl, {
				pointer: pointer.id,
				action: 'dismiss-wp-pointer',
			});
		},
	})
	.pointer( 'open' )
	.pointer( 'sendToTop' );

	x( document ).on( 'wp-window-resized', function() {
		x( pointer.target ).pointer( 'reposition' );
	});
};

GLSR.textareaResize = function( el )
{
	var minHeight = 320;
	var textarea  = el[0];

	textarea.style.height = 'auto';

	textarea.style.height = textarea.scrollHeight > minHeight ?
		textarea.scrollHeight + 'px' :
		minHeight + 'px';
};

GLSR.toggleHiddenField = function( el, bool )
{
	var row = x( el ).closest( '.glsr-field' );

	if( !row.length )return;

	if( bool ) {
		row.removeClass( 'hidden' );
	}
	else {
		row.addClass( 'hidden' );
	}
};

GLSR.pinned.events = function()
{
	var pinnedSelect = x( '#pinned-status-select' );

	x( 'a.cancel-pinned-status' ).on( 'click', function( e ) {
		e.preventDefault();
		pinnedSelect.slideUp( 'fast' ).siblings( 'a.edit-pinned-status' ).show().focus();
		pinnedSelect.find( 'select' ).val( x( '#hidden-pinned-status' ).val() === '0' ? 1 : 0 );
	});

	x( 'a.edit-pinned-status' ).on( 'click', function( e ) {
		e.preventDefault();
		if( pinnedSelect.is( ':hidden' ) ) {
			pinnedSelect.slideDown( 'fast', function() {
				pinnedSelect.find( 'select' ).focus();
			});
			x( this ).hide();
		}
	});

	x( 'a.save-pinned-status' ).on( 'click', function( e ) {
		e.preventDefault();
		pinnedSelect.slideUp( 'fast' ).siblings( 'a.edit-pinned-status' ).show().focus();
		GLSR.pinned.save( x( this ) );
	});

	x( 'table' ).on( 'click', 'td.sticky i', GLSR.pinned.onToggle );
};

GLSR.pinned.onToggle = function()
{
	var el = x( this );

	var data = {
		action: site_reviews.action,
		request: {
			action: 'toggle-pinned',
			id: el[0].getAttribute( 'data-id' ),
		},
	};

	x.post( site_reviews.ajaxurl, data, function( response ) {
		if( response.pinned ) {
			el.addClass( 'pinned' );
		}
		else {
			el.removeClass( 'pinned' );
		}
	});
};

GLSR.pinned.save = function( el )
{
	var data = {
		action: site_reviews.action,
		request: {
			action: 'toggle-pinned',
			id:     x( '#post_ID' ).val(),
			pinned: x( '#pinned-status' ).val(),
		},
	};

	x.post( site_reviews.ajaxurl, data, function( response ) {
		x( '#pinned-status' ).val( !response.pinned|0 );
		x( '#hidden-pinned-status' ).val( response.pinned|0 );
		x( '#pinned-status-text' ).text( response.pinned ? el.data( 'yes' ) : el.data( 'no' ) );

		GLSR.insertNotices( response.notices );
	});
};

x( function()
{
	var GLSR_fix = GLSR.getURLParameter( 'fix' );
	var GLSR_textarea = x( '#contentdiv > textarea' );

	if( GLSR_fix ) {
		x( 'td [data-key="' + GLSR_fix + '"]').focus();
	}

	if( GLSR_textarea.length ) {
		GLSR.textareaResize( GLSR_textarea );

		x( document ).on( 'wp-window-resized.editor-expand', function() {
			GLSR.textareaResize( GLSR_textarea );
		});
	}

	x( 'form' ).on( 'change', ':input', GLSR.onFieldChange );

	x( 'form' ).on( 'click', '#clear-log', GLSR.onClearLog );

	GLSR.colorControls();

	GLSR.pinned.events();

	x.each( site_reviews_pointers.pointers, function( i, pointer ) {
		GLSR.pointers( pointer );
	});

	// WP 4.0 - 4.2 support: toggle list table rows on small screens
	x( document ).on( 'click', '.branch-4 .toggle-row, .branch-4-1 .toggle-row, .branch-4-2 .toggle-row', function() {
		x( this ).closest( 'tr' ).toggleClass( 'is-expanded' );
	});
});
