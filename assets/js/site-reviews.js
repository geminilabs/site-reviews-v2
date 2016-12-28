/**!
 * Star Rating
 *
 * Version: 1.3.0
 * Author: Paul Ryley (http://geminilabs.io)
 * URL: https://github.com/geminilabs/star-rating.js
 * License: MIT
 */

;(function( window, document, undefined )
{
	"use strict";

	var Plugin = function( el, options )
	{
		this.el = el;
		this.options = options;
		this.metadata = this.el.getAttribute( 'data-options' );
		this.stars = 0;

		this.init();
	};

	Plugin.prototype = {

		defaults: {
			clearable  : true,
			initialText: "Click to Rate",
			onClick    : null,
			showText   : true,
		},

		init: function()
		{
			if( this.el.tagName !== 'SELECT' )return;

			for( var i = 0; i < this.el.length; i++ ) {
				if( this.el[i].value === '' )continue;
				// abort if any value is not numerical
				if( isNaN( parseFloat( this.el[i].value )) || !isFinite( this.el[i].value ))return;
				this.stars++;
			}

			// abort if number of stars is outside the 1-10 range
			if( this.stars < 1 || this.stars > 10 )return;

			this.config = this._extend( {}, this.defaults, this.options, this.metadata );

			this.build();

			this._on( "change", this.el, this.change.bind( this ));
			this._on( "mouseenter", this.wrap, this.enter.bind( this ));
			this._on( "mouseleave", this.wrap, this.leave.bind( this ));
			this._on( "click", this.wrap, this.select.bind( this ));
			this._on( "reset", this.el.closest( 'form' ), this.clear.bind( this ));

			this.current = this.el.options[ this.el.selectedIndex ].value;
			this.selected = this.current;
			this.width = this.wrap.offsetWidth;
			this.star = Math.round( this.width / this.stars );

			this.select();

			return this;
		},

		build: function()
		{
			var ordered = {};
			var unordered = {};

			var wrapper = this._createEl( "span", {
				"class": "gl-star-rating",
				"data-star-rating": "",
			});

			this.el.parentNode.insertBefore( wrapper, this.el );
			wrapper.appendChild( this.el );

			this.wrap = this._insertAfterEl( this.el, "span", {
				"class": "gl-star-rating-stars",
			});

			if( this.config.showText ) {
				this.text = this._insertAfterEl( this.wrap, "span", {
					"class": "gl-star-rating-text",
				});
			}

			for( var i = 0; i < this.el.length; i++ ) {
				if( this.el[i].value !== '' ) {
					unordered[ this.el[i].value ] = this.el[i].text;
				}
			}

			Object.keys( unordered ).sort().forEach( function( key ) {
				ordered[key] = unordered[key];
			});

			for( var key in ordered ) {
				this._appendTo( this.wrap, 'span', {
					"data-value": key,
					"data-text": ordered[ key ],
				});
			}
		},

		change: function()
		{
			this.show( this.el.options[ this.el.selectedIndex ].value );
		},

		clear: function( ev )
		{
			if( this.config.clearable || ev !== undefined ) {
				this.el.value = "";
				this.selected = "";
				this.show(0);
			}
		},

		enter: function()
		{
			var rect = this.wrap.getBoundingClientRect();

			this._on( "mousemove", this.wrap, this.move.bind( this ));
			this.offsetLeft = rect.left + document.body.scrollLeft;
		},

		getIndexFromPosition: function( pageX )
		{
			return Math.min(
				Math.ceil( Math.max( pageX - this.offsetLeft, 1 ) / this.star ),
				this.stars
			);
		},

		leave: function()
		{
			this._off( "mousemove", this.wrap, this.move );
			this.show( this.selected );
		},

		move: function( ev )
		{
			this.show( this.getIndexFromPosition( ev.pageX ));
		},

		select: function( ev )
		{
			var index = this.current;

			if( ev !== undefined ) {
				index = this.getIndexFromPosition( ev.pageX );
				if( this.current !== '' && parseFloat( this.selected ) === index ) {
					this.clear();
					return;
				}
			}

			this.el.value = index;
			this.selected = index;

			this.show( index );

			if( ev !== undefined && typeof this.config.onClick === 'function' ) {
				this.config.onClick( this.el );
			}
		},

		show: function( index )
		{
			if( index < 0 || index === "" ) {
				index = 0;
			}
			if( index > this.stars ) {
				index = this.stars;
			}

			this._removeClass( this.wrap, 's' + ( 10 * this.current ));
			this._addClass( this.wrap, 's' + ( 10 * index ));

			if( this.config.showText ) {
				this.text.textContent = index < 1 ? this.config.initialText : this.wrap.childNodes[ index - 1 ].dataset.text;
			}

			this.current = index;
		},

		_addClass: function( el, className )
		{
			if( el.classList ) el.classList.add( className );
			else if( !this._hasClass( el, className )) el.className += ' ' + className;
		},

		_appendTo: function( el, tag, attributes )
		{
			var newEl = this._createEl( tag, attributes );
			el.innerHTML += newEl.outerHTML;
		},

		_createEl: function( tag, attributes )
		{
			var el = ( typeof tag === 'string' ) ? document.createElement( tag ) : tag;

			attributes = attributes || {};

			for( var key in attributes ) {
				el.setAttribute( key, attributes[ key ] );
			}

			return el;
		},

		/**
		 * https://github.com/angus-c/just#just-extend
		 */
		_extend: function()
		{
			var args = [].slice.call( arguments );
			var deep = false;
			if( typeof args[0] === 'boolean' ) {
				deep = args.shift();
			}
			var result = args[0];
			var extenders = args.slice(1);
			var len = extenders.length;
			for( var i = 0; i < len; i++ ) {
				var extender = extenders[i];
				for( var key in extender ) {
					var value = extender[ key ];
					if( deep && value && ( typeof value == 'object' )) {
						var base = Array.isArray( value ) ? [] : {};
						result[ key ] = this._extend( true, base, value );
					}
					else {
						result[ key ] = value;
					}
				}
			}
			return result;
		},

		_hasClass: function( el, className )
		{
			if( el.classList ) return el.classList.contains( className );
			else return new RegExp( '\\b' + className + '\\b' ).test( el.className );
		},

		_insertAfterEl: function( el, tag, attributes )
		{
			var newEl = this._createEl( tag, attributes );
			el.parentNode.insertBefore( newEl, el.nextSibling );

			return newEl;
		},

		_off: function( type, el, handler )
		{
			if( el.detachEvent ) el.detachEvent( 'on' + type, handler );
			else el.removeEventListener( type, handler );
		},

		_on: function( type, el, handler )
		{
			if( el.attachEvent ) el.attachEvent( 'on' + type, handler );
			else el.addEventListener( type, handler );
		},

		_removeClass: function( el, className )
		{
			if( el.classList ) el.classList.remove( className );
			else el.className = el.className.replace( new RegExp( '\\b' + className + '\\b', 'g' ), '' );
		},
	};

	Plugin.defaults = Plugin.prototype.defaults;

	if( window.jQuery ) {
		jQuery.fn.starrating = function( options ) {
			return this.each( function() {
				if( !jQuery.data( this, "plugin_starrating" )) {
					jQuery.data( this, "plugin_starrating", new Plugin( this, options ));
				}
			});
		};
	}

	window.StarRating = Plugin;

})( window, document );

if( this.Element ) {
	(function( ElementPrototype )
	{
		// matches polyfill
		ElementPrototype.matches = ElementPrototype.matches ||
		ElementPrototype.matchesSelector ||
		ElementPrototype.webkitMatchesSelector ||
		ElementPrototype.msMatchesSelector ||
		function( selector )
		{
			var node = this;
			var nodes = ( node.parentNode || node.document ).querySelectorAll( selector );
			var i = -1;
			while( nodes[++i] && nodes[i] !== node );
			return !!nodes[i];
		};

		// closest polyfill
		ElementPrototype.closest = ElementPrototype.closest ||
		function( selector )
		{
			var el = this;
			while( el.matches && !el.matches( selector )) el = el.parentNode;
			return el.matches ? el : null;
		};
	})( Element.prototype );
}

/* jshint browser:true, globalstrict:true, esversion:6 */
/* global console, ActiveXObject, XMLHttpRequest */

"use strict";

var GLSR = {};

GLSR.addClass = function( el, className )
{
	if( el.classList ) el.classList.add( className );
	else if( !GLSR.hasClass( el, className )) el.className += ' ' + className;
};

GLSR.convertValue = function( value )
{
	if( GLSR.isNumeric( value )) {
		return parseFloat( value );
	}
	else if( value === 'true') {
		return true;
	}
	else if( value === 'false' ) {
		return false;
	}
	else if( value === '' || value === null ) {
		return undefined;
	}

	return value;
};

GLSR.hasClass = function( el, className )
{
	if( el.classList ) return el.classList.contains( className );
	else return new RegExp( '\\b' + className + '\\b' ).test( el.className );
};

GLSR.inArray = function( needle, haystack )
{
	var length = haystack.length;
	while( length-- ) {
		if( haystack[ length ] === needle ) {
			return true;
		}
	}

	return false;
};

GLSR.isNumeric = function( value )
{
	return !( isNaN( parseFloat( value )) || !isFinite( value ));
};

GLSR.on = function( type, el, handler )
{
	[].forEach.call( document.querySelectorAll( el ), function( node ) {
		if( node.attachEvent ) node.attachEvent( 'on' + type, handler );
		else node.addEventListener( type, handler );
	});
};

GLSR.off = function( type, el, handler )
{
	[].forEach.call( document.querySelectorAll( el ), function( node ) {
		if( node.detachEvent ) node.detachEvent( 'on' + type, handler );
		else node.removeEventListener( type, handler );
	});
};

/**
 * Adapted from https://github.com/bitovi/jquerypp/blob/master/dom/form_params/form_params.js
 */
GLSR.parseFormData = function( form, convert )
{
	convert = !!convert || false;

	var keyBreaker = /[^\[\]]+/g; // used to parse bracket notation
	var data = {};
	var seen = {}; // used to uniquely track seen values
	var nestData = function( field, data, parts, seenName )
	{
		var name = parts.shift();

		// Keep track of the dot separated fullname
		seenName = seenName ? seenName + '.' + name : name;

		if( parts.length ) {
			if( !data[ name ] ) {
				data[ name ] = {};
			}

			// Recursive call
			nestData( field, data[ name ], parts, seenName );
		}
		else {
			// Convert the value
			var value = convert ? GLSR.convertValue( field.value ) : field.value;

			// Handle same name case, as well as "last checkbox checked" case
			if( seenName in seen && field.type !== "radio" && !data[ name ].isArray()) {
				if( name in data ) {
					data[ name ] = [ data[name] ];
				}
				else {
					data[ name ] = [];
				}
			}
			else {
				seen[ seenName ] = true;
			}

			// Finally, assign data
			if( GLSR.inArray( field.type, ['radio','checkbox'] ) && !field.checked )return;

			if( !data[ name ] ) {
				data[ name ] = value;
			}
			else {
				data[ name ].push( value );
			}
		}
	};

	for( var i = 0; i < form.length; i++ ) {

		var field = form[i];

		if( !field.name || field.disabled || GLSR.inArray( field.type, ['file','reset','submit','button'] ))continue;

		var parts = field.name.match( keyBreaker );

		if( !parts.length ) {
			parts = [ field.name ];
		}

		nestData( field, data, parts );
	}

	return data;
};

GLSR.postAjax = function( url, data, success )
{
	var params = typeof data !== 'string' ? GLSR.serialize( data ) : data;
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject( "Microsoft.XMLHTTP" );

	xhr.open( 'POST', url ); // asynchronously

	xhr.onreadystatechange = function() {
		if( xhr.readyState > 3 && xhr.status === 200 ) {
			success( JSON.parse( xhr.responseText ));
		}
	};

	xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
	xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );

	xhr.send( params );

	return xhr;
};

GLSR.ready = function( fn )
{
	if( typeof fn !== "function" )return;

	// in case the document is already rendered
	if( document.readyState !== 'loading' ) fn();
	// modern browsers
	else if( document.addEventListener ) document.addEventListener( 'DOMContentLoaded', fn );
	// IE <= 8
	else document.attachEvent( 'onreadystatechange', function() {
		if( document.readyState === 'complete' ) fn();
	});
};

GLSR.removeClass = function( el, className )
{
	if( el.classList ) el.classList.remove( className );
	else el.className = el.className.replace( new RegExp( '\\b' + className + '\\b', 'g' ), '' );
};

GLSR.serialize = function( obj, prefix )
{
	var str = [];

	for( var property in obj ) {
		if( !obj.hasOwnProperty( property ))continue;

		var key = prefix ? prefix + "[" + property + "]" : property;
		var value = obj[ property ];

		str.push( typeof value == "object" ?
			GLSR.serialize( value, key ) :
			encodeURIComponent( key ) + "=" + encodeURIComponent( value )
		);
	}

	return str.join( "&" );
};

GLSR.insertAfter = function( el, tag, attributes )
{
	var newEl = GLSR.createEl( tag, attributes );
	el.parentNode.insertBefore( newEl, el.nextSibling );

	return newEl;
};

GLSR.appendTo = function( el, tag, attributes )
{
	var newEl = GLSR.createEl( tag, attributes );
	el.appendChild( newEl );

	return newEl;
};

GLSR.createEl = function( tag, attributes )
{
	var el = ( typeof tag === 'string' ) ? document.createElement( tag ) : tag;

	attributes = attributes || {};

	for( var key in attributes ) {
		el.setAttribute( key, attributes[ key ] );
	}

	return el;
};

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

//# sourceMappingURL=site-reviews.js.map
