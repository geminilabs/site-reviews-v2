"use strict";

var args            = require( 'yargs' ).argv;
var gulp            = require( 'gulp' );
var bump            = require( 'gulp-bump' );
var checktextdomain = require( 'gulp-checktextdomain' );
var potomo          = require( 'gulp-potomo' );
var pseudo          = require( 'gulp-pseudo-i18n' );
var rename          = require( 'gulp-rename' );
var sort            = require( 'gulp-sort' );
var wpPot           = require( 'gulp-wp-pot' );
var elixir          = require( 'laravel-elixir' );
var runSequence     = require( 'run-sequence' );

require( 'elixir-jshint' );

var paths  = {
	src : 'src/',
	dest: 'assets/',
	npm : '../../node_modules/',
	bump: {
		'stable tag': 'readme.txt',
		'version': 'site-reviews.php',
	},
};

elixir.config.assetsPath = paths.src;
elixir.config.publicPath = paths.dest;

elixir(( mix ) => mix
	.jshint( paths.src + 'js/*.js' )
	.scripts( 'mce-plugin.js' )
	.scripts( 'recaptcha.js' )
	.scripts([
		'admin/init.js',
		'admin/functions.js',
		'admin/pinned.js',
		'admin/shortcode.js',
		'admin/translation.js',
		'admin/ready.js',
	], paths.dest + 'js/site-reviews-admin.js' )
	.scripts([
		paths.npm + 'star-rating.js/dist/star-rating.js',
		'helper-functions.js',
		'partials/partial-form.js',
		'partials/partial-recaptcha.js',
		'site-reviews.js',
	], paths.dest + 'js/site-reviews.js' )
	.sass( 'site-reviews-admin.scss' )
	.sass( 'site-reviews.scss' )
	.sass( 'twenty-ten.scss' )
	.sass( 'twenty-eleven.scss' )
	.sass( 'twenty-twelve.scss' )
	.sass( 'twenty-thirteen.scss' )
	.sass( 'twenty-fourteen.scss' )
	.sass( 'twenty-fifteen.scss' )
	.sass( 'twenty-sixteen.scss' )
	.sass( 'twenty-seventeen.scss' )
);

/* Language Tasks
 -------------------------------------------------- */

gulp.task( 'checktextdomain', () => gulp
	.src(['plugin/**/*.php','views/**/*.php'])
	.pipe( checktextdomain({
		text_domain: 'site-reviews',
		keywords: [
			'__:1,2d',
			'_e:1,2d',
			'_x:1,2c,3d',
			'esc_html__:1,2d',
			'esc_html_e:1,2d',
			'esc_html_x:1,2c,3d',
			'esc_attr__:1,2d',
			'esc_attr_e:1,2d',
			'esc_attr_x:1,2c,3d',
			'_ex:1,2c,3d',
			'_n:1,2,4d',
			'_nx:1,2,4c,5d',
			'_n_noop:1,2,3d',
			'_nx_noop:1,2,3c,4d',
		],
	}))
);

gulp.task( 'pot', () => gulp
	.src(['*.php', 'plugin/**/*.php', 'views/**/*.php'])
	.pipe( sort())
	.pipe( wpPot({
		domain        : 'site-reviews',
		destFile      : 'site-reviews.pot',
		package       : 'site-reviews',
		bugReport     : 'https://github.com/geminilabs/site-reviews/issues/new',
		lastTranslator: 'Paul Ryley <paul@geminilabs.io>',
		team          : 'Gemini Labs <support@geminilabs.io>',
	}))
	.pipe( gulp.dest( 'languages' ))
);

gulp.task( 'pseudo', () => gulp
	.src( 'languages/**/*.pot' )
	.pipe( pseudo({
		charMap: {},
	}))
	.pipe( rename( 'site-reviews-en_US.po' ))
	.pipe( gulp.dest( 'languages' ))
);

gulp.task( 'potomo', () => gulp
	.src( 'languages/**/*.po' )
	.pipe( potomo())
	.pipe( gulp.dest( 'languages' ))
);

gulp.task( 'languages', () => runSequence( 'checktextdomain', 'pot', 'pseudo', 'potomo' ));

/* Version Bump Tasks
 -------------------------------------------------- */

gulp.task( 'bump', function() {
	['patch', 'minor', 'major'].some( function( arg ) {
		if( !args[arg] )return;
		for( var key in paths.bump ) {
			gulp.src( paths.bump[key] ).pipe( bump({ type: arg, key: key })).pipe( gulp.dest('.'));
		}
		return true;
	});
});
