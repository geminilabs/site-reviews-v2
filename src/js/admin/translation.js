/** global: _, GLSR, site_reviews, wp, x */

GLSR.translation.abort = function()
{
	if( 'undefined' === typeof GLSR.translation.searchRequest )return;
	GLSR.translation.searchRequest.abort();
};

GLSR.translation.clearResults = function()
{
	GLSR.translation.abort();
	GLSR.translation._.resultsEl.empty();
	x( 'body' ).removeClass( 'glsr-focus loading-content' );
};

GLSR.translation.deleteEntry = function( index )
{
	var row = GLSR.translation._.entriesEl.children( 'tr' ).eq( index );
	row.find( 'td' ).css({ backgroundColor:'#faafaa' });
	row.fadeOut( 350, function() {
		x( this ).remove();
		GLSR.translation._.results = {};
		GLSR.translation.reindexRows();
		GLSR.translation.setVisibility();
	});
};

GLSR.translation.displayResults = function( items )
{
	x( 'body' ).addClass( 'glsr-focus' );
	GLSR.translation._.resultsEl.append( items );
	GLSR.translation._.resultsEl.children( 'span' ).on( 'click', GLSR.translation.onResultClick );
}

GLSR.translation.init = function()
{
	GLSR.translation._ = {
		entriesEl: x( '.glsr-translations tbody' ),
		exclude: [],
		noResults: '',
		results: {},
		resultsEl: x( '.glsr-strings-results' ),
		searchEl: x( '.glsr-strings-search' ),
		selected: -1,
		selectedClass: 'glsr-selected-result',
	};
	if( GLSR.translation._.searchEl.length ) {
		GLSR.translation.makeSortable();
		GLSR.translation._.searchEl.attr( 'aria-describedby', 'live-search-desc' );
		GLSR.translation._.searchEl.on( 'input', _.debounce( GLSR.translation.onSearchInput, 500 ));
		GLSR.translation._.searchEl.on( 'keyup', GLSR.translation.onSearchKeyup );
		GLSR.translation._.entriesEl.on( 'click', 'a.delete', GLSR.translation.onEntryDelete );
		x( document ).on( 'click', GLSR.translation.onDocumentClick );
		x( document ).on( 'keydown', GLSR.translation.onDocumentKeydown );
	}
};

GLSR.translation.makeSortable = function()
{
	GLSR.translation._.entriesEl.sortable({
		axis: 'y',
		items: 'tr',
		tolerance: 'pointer',
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

GLSR.translation.navigateResults = function( diff )
{
	GLSR.translation._.selected += diff;
	GLSR.translation._.results.removeClass( GLSR.translation._.selectedClass );
	if( GLSR.translation._.selected < 0 ) {
		// reached the top (should now allow keydown scroll)
		GLSR.translation._.selected = -1;
		GLSR.translation._.searchEl.focus();
	}
	if( GLSR.translation._.selected >= GLSR.translation._.results.length ) {
		// reached the end (should now allow keydown scroll)
		GLSR.translation._.selected = GLSR.translation._.results.length - 1;
	}
	if( GLSR.translation._.selected >= 0 ) {
		GLSR.translation._.results.eq( GLSR.translation._.selected )
			.addClass( GLSR.translation._.selectedClass )
			.focus();
	}
};

GLSR.translation.onDocumentClick = function( ev )
{
	var searchForm = ev.target.closest( '.glsr-strings' );
	if( !searchForm && x( 'body' ).hasClass( 'glsr-focus' )) {
		GLSR.translation.clearResults();
	}
};

GLSR.translation.onDocumentKeydown = function( ev )
{
	if( !GLSR.translation._.results )return;
	if( GLSR.keys.ESC === ev.which ) {
		GLSR.translation.clearResults();
	}
	if( GLSR.keys.ENTER === ev.which || GLSR.keys.SPACE === ev.which ) {
		var selected = GLSR.translation._.resultsEl.find( '.' + GLSR.translation._.selectedClass );
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

GLSR.translation.onEntryDelete = function( ev )
{
	ev.preventDefault();
	GLSR.translation.deleteEntry( x( this ).closest( 'tr' ).index() );
};

GLSR.translation.onResultClick = function( ev )
{
	ev.preventDefault();
	var result = x( this );
	var entry = result.data( 'entry' );
	var template = wp.template( 'glsr-string-' + ( entry.plural ? 'plural' : 'single' ));
	entry.index = GLSR.translation._.entriesEl.children().length;
	entry.prefix = GLSR.translation._.resultsEl.data( 'prefix' );
	entry.msgid = entry.single;
	entry.msgid_plural = entry.plural;
	if( template ) {
		GLSR.translation._.entriesEl.append( template( entry ));
		GLSR.translation._.exclude.push({ id: entry.id });
		GLSR.translation._.results = GLSR.translation._.results.filter( function( i, el ) {
			return el !== result.get(0);
		});
	}
	GLSR.translation.setVisibility();
	GLSR.translation.clearResults();
};

GLSR.translation.onSearchInput = function( ev )
{
	GLSR.translation.abort();
	if( GLSR.translation.searchTerm === ev.target.value && GLSR.translation._.results.length ) {
		return GLSR.translation.displayResults( GLSR.translation._.results );
	}
	GLSR.translation._.resultsEl.empty();
	GLSR.translation._.selected = -1;
	GLSR.translation.searchTerm = ev.target.value;
	if( GLSR.translation.searchTerm === '' ) {
		return GLSR.translation.reset();
	}
	x( 'body' ).addClass( 'loading-content' );
	GLSR.translation.searchRequest = wp.ajax.post( site_reviews.action, {
		_nonce: site_reviews.ajaxnonce,
		request: {
			action: 'search-translations',
			exclude: GLSR.translation._.exclude,
			search: ev.target.value,
		},
	}).done( function( response ) {
		x( 'body' ).removeClass( 'loading-content' );
		GLSR.translation.displayResults( response.items ? response.items : response.empty );
		GLSR.translation._.results = GLSR.translation._.resultsEl.children();
		delete GLSR.translation.searchRequest;
	});
};

GLSR.translation.onSearchKeyup = function( ev )
{
	if( GLSR.keys.ESC === ev.which ) {
		GLSR.translation.reset();
	}
	if( GLSR.keys.ENTER === ev.which ) {
		GLSR.translation.onSearchInput( ev );
	}
}

GLSR.translation.reset = function()
{
	GLSR.translation.clearResults();
	GLSR.translation._.results = {};
	GLSR.translation._.searchEl.val( '' );
};

GLSR.translation.reindexRows = function()
{
	GLSR.translation._.exclude = [];
	GLSR.translation._.entriesEl.children( 'tr' ).each( function( index ) {
		x( this ).find( '.glsr-string-translation' ).children().filter( ':input' ).each( function() {
			var input = x( this );
			var name = input.attr( 'name' ).replace( /\[\d+\]/i, '[' + index + ']' );
			input.attr( 'name', name );
			if( input.attr( 'type' ) === 'hidden' ) {
				GLSR.translation._.exclude.push({ id: input.val() })
			}
		});
	});
};

GLSR.translation.setVisibility = function()
{
	var action = GLSR.translation._.entriesEl.children().length > 0 ? 'remove' : 'add';
	GLSR.translation._.entriesEl.parent()[action + 'Class']( 'glsr-hidden' );
};
