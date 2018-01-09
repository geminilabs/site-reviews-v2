/* jshint browser:true, globalstrict:true, esversion:6 */
/* global console, CustomEvent, GLSR, grecaptcha, HTMLFormElement, site_reviews, StarRating */

"use strict";

GLSR.SCROLL_TIME = 468;

GLSR.createExceprts = function( parentEl )
{
	parentEl = parentEl || document;
	var excerpts = parentEl.querySelectorAll( '.glsr-hidden-text' );
	for( var i = 0; i < excerpts.length; i++ ) {
		var readmore = GLSR.insertAfter( excerpts[i], 'span', {
			'class': 'glsr-read-more',
		});
		var readmoreLink = GLSR.appendTo( readmore, 'a', {
			'href': '#',
			'data-text': excerpts[i].getAttribute( 'data-show-less' ),
		});
		readmoreLink.innerHTML = excerpts[i].getAttribute( 'data-show-more' );
	}
	GLSR.on( 'click', '.glsr-read-more a', GLSR.onClickReadMore );
};

GLSR.createStarRatings = function()
{
	var ratings = document.querySelectorAll( 'select.glsr-star-rating' );
	for( var i = 0; i < ratings.length; i++ ) {
		new StarRating( ratings[i], {
			clearable: false,
			showText : false,
			onClick  : GLSR.clearFieldError,
		});
	}
};

GLSR.getSelectorOfElement = function( el )
{
	if( !el || el.nodeType !== el.ELEMENT_NODE )return;
	return el.nodeName.toLowerCase() +
		( el.id ? '#' + el.id.trim() : '' ) +
		( el.className ? '.' + el.className.trim().replace( /\s+/g, '.' ) : '' );
};

GLSR.now = function()
{
	return ( window.performance && window.performance.now ) ? window.performance.now() : Date.now();
};

GLSR.onClickPagination = function( ev )
{
	ev.preventDefault();
	var parentEl = this.closest( '.glsr-reviews' );
	var parentSelector = GLSR.getSelectorOfElement( parentEl );
	GLSR.addClass( parentEl, 'glsr-hide' );
	GLSR.getAjax( this.href, function( response ) {
		var html = document.implementation.createHTMLDocument( 'new' );
		html.documentElement.innerHTML = response;
		var newParentEl = parentSelector ? html.querySelectorAll( parentSelector ) : '';
		if( newParentEl.length === 1 ) {
			parentEl.innerHTML = newParentEl[0].innerHTML;
			GLSR.scrollToTop( parentEl );
			GLSR.removeClass( parentEl, 'glsr-hide' );
			GLSR.on( 'click', '.glsr-ajax-navigation a', GLSR.onClickPagination );
			window.history.pushState( null, '', ev.target.href );
			GLSR.createExceprts( parentEl );
			return;
		}
		window.location = ev.target.href;
	});
};

GLSR.onClickReadMore = function( ev )
{
	ev.preventDefault();
	var el = ev.target;
	var hiddenNode = el.parentNode.previousSibling;
	var text = el.getAttribute( 'data-text' );
	GLSR.toggleClass( hiddenNode, 'glsr-hidden' );
	GLSR.toggleClass( hiddenNode, 'glsr-visible' );
	el.setAttribute( 'data-text', el.innerText );
	el.innerText = text;
};

GLSR.scrollToTop = function( el, offset )
{
	offset = offset || 16; // 1rem
	var fixedElement;
	for( var i = 0; i < site_reviews.ajaxpagination.length; i++ ) {
		fixedElement = document.querySelector( site_reviews.ajaxpagination[i] );
		if( fixedElement && window.getComputedStyle( fixedElement ).getPropertyValue( 'position' ) === 'fixed' ) {
			offset = offset + fixedElement.clientHeight;
		}
	}
	var clientBounds = el.getBoundingClientRect();
	var offsetTop = clientBounds.top - offset;
	if( offsetTop > 0 )return; // if top is in view, don't scroll!
	if( 'requestAnimationFrame' in window === false ) {
		window.scroll( 0, window.pageYOffset + offsetTop );
		return;
	}
	GLSR.scrollToTopStep({
		endY: offsetTop,
		offset: window.pageYOffset,
		startTime: GLSR.now(),
		startY: el.scrollTop,
	});
};

GLSR.scrollToTopStep = function( context )
{
	var elapsed = ( GLSR.now() - context.startTime ) / GLSR.SCROLL_TIME;
	elapsed = elapsed > 1 ? 1 : elapsed;
	var easedValue = 0.5 * ( 1 - Math.cos( Math.PI * elapsed ));
	var currentY = context.startY + ( context.endY - context.startY ) * easedValue;
	window.scroll( 0, context.offset + currentY );
	if( currentY !== context.endY ) {
		window.requestAnimationFrame( GLSR.scrollToTopStep.bind( window, context ));
	}
};

GLSR.on( 'click', '.glsr-ajax-navigation a', GLSR.onClickPagination );

GLSR.ready( function()
{
	GLSR.createExceprts();
	GLSR.createStarRatings();
});
