/**
 * @return void
 */
GLSR.translation.abort = function()
{
	if( 'undefined' !== typeof GLSR.translation.searchRequest ) {
		GLSR.translation.searchRequest.abort();
	}
};

/**
 * @return void
 */
GLSR.translation.clearResults = function()
{
	if( 'undefined' !== typeof GLSR.translation.searchRequest ) {
		GLSR.translation.searchRequest.abort();
	}
	GLSR.translation.searchEl.value = '';
	GLSR.translation.resultsEl.empty();
	x( 'body' ).removeClass( 'glsr-focus loading-content' );
};

/**
 * @return void
 */
GLSR.translation.init = function()
{
	GLSR.translation.exclude = [];
	GLSR.translation.searchEl = x( '.glsr-strings-search' );
	GLSR.translation.resultsEl = x( '.glsr-strings-results' );
	GLSR.translation.entriesEl = x( '.glsr-translations tbody' );
	if( GLSR.translation.searchEl.length ) {
		GLSR.translation.searchEl.attr( 'aria-describedby', 'live-search-desc' );
		GLSR.translation.searchEl.on( 'input', _.debounce( GLSR.translation.onSearchInput, 500 ));
		GLSR.translation.searchEl.on( 'keyup', GLSR.translation.onSearchKeyup );
	}
	x( document ).on( 'keyup', GLSR.translation.onDocumentKeyup );
};

/**
 * @return void
 */
GLSR.translation.onDocumentKeyup = function( ev )
{
	if( GLSR.keys.ESC === ev.which && GLSR.translation.searchEl.length ) {
		GLSR.translation.clearResults();
	}
};

/**
 * @return void
 */
GLSR.translation.onResultClick = function( ev )
{
	ev.preventDefault();
	var entry = JSON.parse( ev.target.getAttribute( 'data-entry' ));
	var template = wp.template( 'glsr-string-' + ( entry.plural ? 'plural' : 'single' ));
	entry.index = GLSR.translation.entriesEl.children().length;
	entry.prefix = GLSR.translation.resultsEl.data( 'prefix' );
	entry.msgid = entry.single;
	entry.msgid_plural = entry.plural;
	if( template ) {
		GLSR.translation.entriesEl.append( template( entry ));
	}
	GLSR.translation.exclude.push({ id: entry.id })
	GLSR.translation.clearResults();
};

/**
 * @return void
 */
GLSR.translation.onSearchInput = function( ev )
{
	if( GLSR.translation.searchTerm === ev.target.value )return;
	GLSR.translation.abort();
	GLSR.translation.resultsEl.empty();
	GLSR.translation.searchTerm = ev.target.value;
	if( GLSR.translation.searchTerm === '' ) {
		return GLSR.translation.clearResults();
	}
	x( 'body' ).addClass( 'loading-content' );
	GLSR.translation.searchRequest = wp.ajax.post( site_reviews.action, {
		_nonce: site_reviews.ajaxnonce,
		request: {
			action: 'search-translations',
			exclude: GLSR.translation.exclude,
			search: ev.target.value,
		},
	}).done( function( response ) {
		x( 'body' )[( response.items.length ? 'add' : 'remove' ) + 'Class']( 'glsr-focus' );
		x( 'body' ).removeClass( 'loading-content' );
		GLSR.translation.resultsEl.append( response.items );
		GLSR.translation.resultsEl.children( 'span' ).on( 'click', GLSR.translation.onResultClick );
		delete GLSR.translation.searchRequest;
	});
};

/**
 * @return void
 */
GLSR.translation.onSearchKeyup = function( ev )
{
	if( GLSR.keys.ESC === ev.which ) {
		ev.target.value = '';
	}
	if( GLSR.keys.ENTER === ev.which ) {
		GLSR.translation.abort();
		GLSR.translation.searchTerm = '';
		GLSR.translation.onSearchInput( ev );
	}
}



// window.displayBoxIndex = -1;

// $('#cityresults').on('click', 'a', function () {
//     $('#city').val($(this).text());
//     $('#cityresults').hide('');
//     $('#citygeonameid').val($(this).parent().attr('data-id'));
//     return false;
// });

// var Navigate = function (diff) {
//     displayBoxIndex += diff;
//     var oBoxCollection = $("#cityresults ul li a");
//     if (displayBoxIndex >= oBoxCollection.length) {
//         displayBoxIndex = 0;
//     }
//     if (displayBoxIndex < 0) {
//         displayBoxIndex = oBoxCollection.length - 1;
//     }
//     var cssClass = "display_box_hover";
//     oBoxCollection.removeClass(cssClass).eq(displayBoxIndex).addClass(cssClass);
// }

// $(document).on('keypress keyup', function (e) {
//     if (e.keyCode == 13 || e.keyCode == 32) {
//         $('.display_box_hover').trigger('click');
//         return false;
//     }
//     if (e.keyCode == 40) {
//         //down arrow
//         Navigate(1);
//     }
//     if (e.keyCode == 38) {
//         //up arrow
//         Navigate(-1);
//     }
// });

/**
 * Whether we're waiting for an Ajax request to complete.
 * @type {bool}
 */
// GLSR.strings.ajaxLocked = false;
