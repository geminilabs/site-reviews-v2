(function( tinymce ) {

	tinymce.PluginManager.add( 'glsr_shortcode', function( editor ) {
		editor.addCommand( 'GLSR_Shortcode', function() {
			GLSR.shortcode.create( editor.id );
		});
	});

})( window.tinymce );

//# sourceMappingURL=mce-plugin.js.map
