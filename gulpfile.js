var args            = require('yargs').argv;
var autoprefixer    = require('gulp-autoprefixer');
var bump            = require('gulp-bump');
var checktextdomain = require('gulp-checktextdomain');
var concat          = require('gulp-concat');
var cssnano         = require('gulp-cssnano');
var gulp            = require('gulp');
var gulpif          = require('gulp-if');
var jshint          = require('gulp-jshint');
var mergeStream     = require('merge-stream');
var potomo          = require('gulp-potomo');
var pseudo          = require('gulp-pseudo-i18n');
var pump            = require('pump');
var rename          = require('gulp-rename');
var runSequence     = require('run-sequence');
var sass            = require('gulp-sass');
var sort            = require('gulp-sort');
var uglify          = require('gulp-uglify');
var wpPot           = require('gulp-wp-pot');
var yaml            = require('yamljs');

var config = yaml.load('src/config.yml');

gulp.task('build', function() {
	gulp.start('scss', 'jshint', 'js', 'languages')
});

gulp.task('bump', function() {
	['patch', 'minor', 'major'].some(function(arg) {
		if(!args[arg])return;
		for(var key in config.bump) {
			if(!config.bump.hasOwnProperty(key))continue;
			pump([
				gulp.src(config.bump[key]),
				bump({type:arg,key:key}),
				gulp.dest('.'),
			]);
		}
	});
});

gulp.task('default', function() {
	gulp.start('scss', 'jshint', 'js')
});

gulp.task('js', function() {
	var streams = mergeStream();
	for(var key in config.scripts) {
		streams.add(gulp.src(config.scripts[key]).pipe(concat(key)));
	}
	return pump([
		streams,
		gulpif(args.production, uglify({
			output: {comments: 'some'},
		})),
		gulp.dest(config.dest.js),
	]);
});

gulp.task('jshint', function() {
	return pump([
		gulp.src(config.watch.js),
		jshint(),
		jshint.reporter('jshint-stylish'),
		jshint.reporter('fail'),
	]);
});

gulp.task('languages', function() {
	return runSequence('pot', 'pot-to-po', 'po-to-mo');
});

gulp.task('po-to-mo', function() {
	return pump([
		gulp.src(config.dest.lang + '*.po'),
		potomo(),
		gulp.dest(config.dest.lang),
	]);
});

gulp.task('pot', function() {
	return pump([
		gulp.src(config.watch.php),
		checktextdomain({
			text_domain: config.language.domain,
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
		}),
		sort(),
		wpPot({
			domain: config.language.domain,
			lastTranslator: config.language.translator,
			team: config.language.team,
		}),
		rename(config.language.domain + '.pot'),
		gulp.dest(config.dest.lang),
	]);
});

gulp.task('pot-to-po', function() {
	return pump([
		gulp.src(config.dest.lang + '*.pot'),
		pseudo({
			charMap: {},
		}),
		rename(config.language.domain + '-en_US.po'),
		gulp.dest(config.dest.lang),
	]);
});

gulp.task('scss', function() {
	return pump([
		gulp.src(config.watch.scss),
		sass({
			outputStyle: 'expanded',
		}).on('error', sass.logError),
		autoprefixer('last 2 versions'),
		gulpif(args.production, cssnano({
			minifyFontValues: false,
			discardComments: {removeAll: true},
			zindex: false,
		})),
		gulp.dest(config.dest.css),
	]);
});

gulp.task('watch', function() {
	gulp.watch(config.watch.js, ['jshint', 'js']);
	gulp.watch(config.watch.scss, ['scss']);
});
