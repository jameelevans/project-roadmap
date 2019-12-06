/** 
 * Gulpfile for WordPress with Sass
 *
 * ! What the code does:
 * *    1. Live reloads browser with BrowserSync
 * *    2. CSS: Sass to CSS conversion, error catching, Autoprefixing
 * *    3. JS: Utilies Webpack to bundle js modules into one file
 * *    4. Watches files for changes in CSS or JS
 * *    5. Watches files for changes in PHP
 *      
 *  TODO: Update /settings file to fit project 
 *  TODO: Once project settings is set run --> npm install  
 *  TODO: When instalation ends run --> gulp watch 
 *
 */


/**
 * * Project variables
 *
 * ! These variables do the following:
 *    1. Load all project settings and plugins
 * 
 */

// * Load project settings 
const settings = require('./settings'); // File for all project settings ( Edit this to fit project )

// * Load gulp plugins
const gulp = require('gulp'); // Enables project to use Gulp plugins

// * CSS related plugins
const sass = require('gulp-sass'); // Gulp plugin for Sass compilation
const autoprefixer = require('gulp-autoprefixer'); // Autoprefixing magic

// * JS related plugins
const webpack = require('webpack'); // Plugin to concatenate js modules

// * Image related plugins
const imagemin = require('gulp-imagemin'); // Minify PNG, JPEG, GIF and SVG images with imagemin.

// * Utility related plugins
const browserSync = require('browser-sync').create(); // Reloads browser and injects CSS. Time-saving synchronized browser testing.
const notify = require('gulp-notify'); // Sends message notification to you.
const cache = require('gulp-cache'); // Cache files in stream for later use.


/**
 * * Task: styles
 *
 *
 * ! This task compiles sass and autoprefixes css:
 *    1. Gets the source scss file
 *    2. Compiles Sass to CSS
 *    3. Autoprefixes it and generates style.css
 *    4. Injects CSS or reloads the browser via browserSync
 */

gulp.task('styles', () => {
	return gulp
		.src(settings.styleSRC)
		.pipe(
			sass({
				errLogToConsole: true,
				outputStyle: settings.outputStyle,
				precision: settings.precision
			})
		)
		.pipe(autoprefixer(settings.BROWSERS_LIST))
		.on('error', (error) => console.log(error.toString()))
		.pipe(gulp.dest(settings.styleDestination))
		.pipe(notify({
			message: '\n\n ✅  Styles task completed ✅\n',
			onLast: true
		}));
});


/**
 * * Task: scripts
 *
 *
 * ! This task does the following:
 *     1. Use webpack to bundle js modules
 *	   2. Log errors if there are any
 */

gulp.task('scripts', (callback) => {
	webpack(require(settings.jsWebpack), function (err, stats) {
		if (err) {
			console.log(err.toString());
		}

		console.log(stats.toString());
		callback();
	});
});

/**
 * * Task: images
 *
 *
 * ! This task minifies PNG, JPEG, GIF and SVG images:
 *     1. Gets the source of images raw folder
 *     2. Minifies PNG, JPEG, GIF and SVG images
 *     3. Generates and saves the optimized images
 *
 * This task will run only once, if you want to run it
 * again, do it with the command `gulp images`.
 *
 */

gulp.task('images', () => {
	return gulp
		.src(settings.imgSRC)
		.pipe(
			cache(
				imagemin([
					imagemin.gifsicle({
						interlaced: true
					}),
					imagemin.jpegtran({
						progressive: true
					}),
					imagemin.optipng({
						optimizationLevel: 3
					}), // 0-7 low-high.
					imagemin.svgo({
						plugins: [{
							removeViewBox: true
						}, {
							cleanupIDs: false
						}]
					})
				])
			)
		)
		.pipe(gulp.dest(settings.imgDST))
		.pipe(notify({
			message: '\n\n ✅  Minified images ✅  \n',
			onLast: true
		}));
});

/**
 * * Task: clearCache
 *
 * ! This task deletes the images cache
 *  By running the "images" task each image will be regenerated.
 * 
 */
gulp.task('clearCache', (done) => {
	return cache.clearAll(done);
});


/**
 * * Task: watch and waiting for
 *
 * Watches for file changes and runs all other tasks
 * 
 */

gulp.task('watch', () => {
	browserSync.init({
		notify: false,
		proxy: settings.urlToPreview,
		ghostMode: false
	});

	gulp.watch(settings.watchPhp, (done) => {
		browserSync.reload();
		done();
	});
	gulp.watch(settings.watchStyles, gulp.parallel('waitForStyles'));
	gulp.watch([settings.watchJsModules, settings.watchJsMain], gulp.parallel('waitForScripts'));
	gulp.watch(settings.imgSRC, gulp.series('images')); // Reload on customJS file changes.
});

gulp.task('waitForStyles', gulp.series('styles', () => {
	return gulp.src(settings.styleMain)
		.pipe(browserSync.stream());
}));

gulp.task('waitForScripts', gulp.series('scripts', (cb) => {
	browserSync.reload();
	cb()
}));