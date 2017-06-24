/** global: _, GLSR, site_reviews, wp, x */

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
	GLSR.translation.results = {};
	GLSR.translation.resultsEl.empty();
	x( 'body' ).removeClass( 'glsr-focus loading-content' );
};

/**
 * @return void
 */
GLSR.translation.deleteRow = function( index )
{
	var row = GLSR.translation.entriesEl.children( 'tr' ).eq( index );
	row.find( 'td' ).css({ backgroundColor:'#faafaa' });
	row.fadeOut( 350, function() {
		x( this ).remove();
		GLSR.translation.reIndexRows();
	});
};

/**
 * @return void
 */
GLSR.translation.init = function()
{
	GLSR.translation.exclude = [];
	GLSR.translation.selectedClass = 'glsr-selected-result';
	GLSR.translation.searchEl = x( '.glsr-strings-search' );
	GLSR.translation.resultsEl = x( '.glsr-strings-results' );
	GLSR.translation.entriesEl = x( '.glsr-translations tbody' );
	if( GLSR.translation.searchEl.length ) {
		GLSR.translation.makeSortable();
		GLSR.translation.searchEl.attr( 'aria-describedby', 'live-search-desc' );
		GLSR.translation.searchEl.on( 'input', _.debounce( GLSR.translation.onSearchInput, 500 ));
		GLSR.translation.searchEl.on( 'keyup', GLSR.translation.onSearchKeyup );
		GLSR.translation.entriesEl.on( 'click', 'a.delete', GLSR.translation.onEntryDelete );
		x( document ).on( 'click', GLSR.translation.onDocumentClick );
		x( document ).on( 'keydown', GLSR.translation.onDocumentKeydown );
	}
};

/**
 * @return void
 */
GLSR.translation.makeSortable = function()
{
	GLSR.translation.entriesEl.sortable({
		axis: 'y',
		items: 'tr',
		tolerance: 'pointer',
		forcePlaceholderSize: true,
		start: function( ev, ui ) {
			ui.placeholder.height( ui.helper[0].scrollHeight );
		},
		sort: function( ev, ui ) {
			var top = ev.pageY - x( this ).offsetParent().offset().top - ( ui.helper.outerHeight( true ) / 2 );
			ui.helper.css({
				top: top + 'px',
			});
		},
	});
};

/**
 * @return void
 */
GLSR.translation.navigateResults = function( diff )
{
	GLSR.translation.selected += diff;
	GLSR.translation.results.removeClass( GLSR.translation.selectedClass );
	if( GLSR.translation.selected >= GLSR.translation.results.length ) {
		// reached the end (should now allow keydown scroll)
		GLSR.translation.selected = GLSR.translation.results.length - 1;
	}
	if( GLSR.translation.selected < 0 ) {
		// reached the top (should now allow keydown scroll)
		GLSR.translation.selected = -1;
		GLSR.translation.searchEl.focus();
	}
	if( GLSR.translation.selected >= 0 ) {
		GLSR.translation.results.eq( GLSR.translation.selected )
			.addClass( GLSR.translation.selectedClass )
			.focus();
	}
};

/**
 * @return void
 */
GLSR.translation.onDocumentClick = function( ev )
{
	var searchForm = ev.target.closest( '.glsr-strings' );
	if( !searchForm && x( 'body' ).hasClass( 'glsr-focus' )) {
		GLSR.translation.clearResults();
	}
};

/**
 * @return void
 */
GLSR.translation.onDocumentKeydown = function( ev )
{
	if( !GLSR.translation.results )return;
	if( GLSR.keys.ESC === ev.which ) {
		GLSR.translation.clearResults();
	}
	if( GLSR.keys.ENTER === ev.which || GLSR.keys.SPACE === ev.which ) {
		var selected = GLSR.translation.resultsEl.find( '.' + GLSR.translation.selectedClass );
		if( selected ) {
			selected.trigger( 'click' );
		}
	}
	if( GLSR.keys.UP === ev.which ) {
		GLSR.translation.navigateResults(-1);
		ev.preventDefault();
	}
	if( GLSR.keys.DOWN === ev.which ) {
		GLSR.translation.navigateResults(1);
		ev.preventDefault();
	}
};

/**
 * @return void
 */
GLSR.translation.onEntryDelete = function()
{
	GLSR.translation.deleteRow( x( this ).closest( 'tr' ).index() );
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
	GLSR.translation.selected = -1;
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
		GLSR.translation.results = GLSR.translation.resultsEl.children( 'span' );
		GLSR.translation.results.on( 'click', GLSR.translation.onResultClick );
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

/**
 * @return void
 */
GLSR.translation.reIndexRows = function()
{
	GLSR.translation.entriesEl.children( 'tr' ).each( function( index ) {
		x( this ).find( '.glsr-string-translation' ).children().filter( ':input' ).each( function() {
			var name = x( this ).attr( 'name' ).replace( /\[\d+\]/i, '[' + index + ']' );
			x( this ).attr( 'name', name );
		});
	});
};
