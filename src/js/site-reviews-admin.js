/* jshint browser:true, globalstrict:true, esversion:6 */
/* global ajaxurl, console, jQuery, site_reviews, site_reviews_pointers, tinymce, alert */

"use strict";

var x = jQuery.noConflict();

var GLSR = {
	addons: {},
	pinned: {},
	shortcode: {},
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

GLSR.onClearLog = function( ev )
{
	ev.preventDefault();

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

GLSR.shortcode.close = function( el )
{
	var button = x(( el = el || '.sc-button' ));

	if( button.length ) {
		button.removeClass( 'active' ).parent().find( '.sc-menu' ).hide();
	}
};

GLSR.shortcode.open = function( el )
{
	x( el ).addClass( 'active' ).parent().find( '.sc-menu' ).show();
};

GLSR.shortcode.toggle = function( ev )
{
	ev.preventDefault();

	if( x( this ).hasClass( 'active' ) ) {
		GLSR.shortcode.close( this );
	}
	else {
		GLSR.shortcode.open( this );
	}
};

GLSR.shortcode.trigger = function( ev )
{
	ev.preventDefault();

	// GLSR.shortcode.current is used by scForm to trigger the correct popup
	GLSR.shortcode.current = x( this ).attr( 'data-shortcode' );

	if( !GLSR.shortcode.current )return;

	if( !tinymce.get( window.wpActiveEditor ) ) {
		// Quicktags Editor
		if( !x( '#scTemp' ).length ) {

			x( 'body' ).append( '<textarea id="scTemp" style="display: none;" />' );

			tinymce.init({
				mode     : "exact",
				elements : "scTemp",
				plugins  : ['glsr_shortcode', 'wplink']
			});
		}

		setTimeout( function() {
			tinymce.execCommand( 'GLSR_Shortcode' );
		}, 200 );
	}
	else {
		// TinyMCE Editor
		tinymce.execCommand( 'GLSR_Shortcode' );
	}

	setTimeout( function() {
		GLSR.shortcode.close();
	}, 100 );
};

GLSR.shortcode.create = function( editor_id )
{
	var editor = tinymce.get( editor_id );

	if( !editor )return;

	var data = {
		action: site_reviews.action,
		request: {
			action: 'mce-shortcode',
			shortcode: GLSR.shortcode.current,
		},
	};

	x.post( site_reviews.ajaxurl, data, function( response )
	{
		if( !response.body )return;

		if( response.body.length === 0 ) {
			window.send_to_editor( '[' + response.shortcode + ']' );
			GLSR.shortcode.destroy();
			return;
		}

		var buttons = [{
			text    : response.ok,
			classes : 'primary sc-primary',
			onclick : function() {
				var field, required, valid, win;
				// Get the top most window object
				win = editor.windowManager.getWindows()[0];
				// Get the shortcode required attributes
				required = site_reviews.shortcodes[ GLSR.shortcode.current ];
				valid = true;

				// Do some validation voodoo
				for( var id in required ) {
					if( !required.hasOwnProperty( id ) )continue;

					field = win.find( '#' + id )[0];

					if( typeof field !== 'undefined' && field.state.data.value === '' ) {
						valid = false;
						alert( required[ id ] );
						break;
					}
				}

				if( valid ) {
					win.submit();
				}
			}
		},{
			text    : response.close,
			onclick : 'close'
		}];

		var popup = {
			title   : response.title,
			body    : response.body,
			classes: 'sc-popup',
			minWidth: 320,
			buttons : buttons,
			onsubmit: function( e ) {
				var attributes = '';
				var data = GLSR.shortcode.normalize( e.data );

				for( var key in data ) {
					if( data.hasOwnProperty( key ) && data[ key ] !== '' ) {
						attributes += ' ' + key + '="' + data[ key ] + '"';
					}
				}
				// Insert shortcode into the WP_Editor
				window.send_to_editor( '[' + response.shortcode + attributes + ']' );
			},
			onclose: function() {
				GLSR.shortcode.destroy();
			}
		};

		// Change the buttons if server-side validation failed
		if( response.ok.constructor === Array ) {
			popup.buttons[0].text    = response.ok[0];
			popup.buttons[0].onclick = 'close';
			delete popup.buttons[1];
		}

		editor.windowManager.open( popup );
	});
};

GLSR.shortcode.normalize = function( data )
{
	var shortcodes = {
		'site_reviews'     : ['author','date','excerpt','rating','title','url'],
		'site_reviews_form': ['email','name','review','terms','title'],
	};

	var hide = [];

	for( var key in data ) {
		if( shortcodes.hasOwnProperty( GLSR.shortcode.current ) ) {

			var value = '';

			if( key.lastIndexOf( 'hide_', 0 ) === 0 ) {
				value = key.substring(5);
			}

			if( shortcodes[ GLSR.shortcode.current ].indexOf( value ) > -1 ) {
				if( data[ key ] ) {
					hide.push( value );
				}
				delete data[ key ];
			}
		}

		if( key === 'count' && !x.isNumeric( data[ key ] ) ) {
			data[ key ] = '';
		}

		if( key === 'id' ) {
			data[ key ] = (+new Date()).toString(36);
		}
	}

	data.hide = hide.join( ',' );

	return data;
};

GLSR.shortcode.destroy = function()
{
	var tmp = x( '#scTemp' );

	if( tmp.length ) {
		tinymce.get( 'scTemp' ).remove();
		tmp.remove();
	}
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

	x( document ).on( 'click', function( ev )
	{
		if( !x( ev.target ).closest( '.sc-wrap' ).length ) {
			GLSR.shortcode.close();
		}
	});

	x( document ).on( 'click', '.sc-button', GLSR.shortcode.toggle );
	x( document ).on( 'click', '.sc-shortcode', GLSR.shortcode.trigger );

	// WP 4.0 - 4.2 support: toggle list table rows on small screens
	x( document ).on( 'click', '.branch-4 .toggle-row, .branch-4-1 .toggle-row, .branch-4-2 .toggle-row', function() {
		x( this ).closest( 'tr' ).toggleClass( 'is-expanded' );
	});
});
