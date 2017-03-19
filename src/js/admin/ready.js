/** global: GLSR, site_reviews_pointers, x */

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
		if( !x( ev.target ).closest( '.glsr-mce' ).length ) {
			GLSR.shortcode.close();
		}
	});

	x( document ).on( 'change', 'input#assigned_to', GLSR.onChangeAssignedTo );
	x( document ).on( 'click', '.glsr-mce-button', GLSR.shortcode.toggle );
	x( document ).on( 'click', '.glsr-mce-menu-item', GLSR.shortcode.trigger );
	x( document ).on( 'click', 'a.change-site-review-status', GLSR.onChangeStatus );

	// WP 4.0 - 4.2 support: toggle list table rows on small screens
	x( document ).on( 'click', '.branch-4 .toggle-row, .branch-4-1 .toggle-row, .branch-4-2 .toggle-row', function() {
		x( this ).closest( 'tr' ).toggleClass( 'is-expanded' );
	});
});
