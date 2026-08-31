import gulp from 'gulp';
import gulpSass from 'gulp-sass';
import * as dartSass from 'sass';
import gulpAutoprefixer from 'gulp-autoprefixer';
import browserSync from 'browser-sync';
import settings from './settings.mjs';
import imagemin from 'gulp-imagemin';
import imageminGifsicle from 'imagemin-gifsicle';
import imageminMozjpeg from 'imagemin-mozjpeg';
import imageminOptipng from 'imagemin-optipng';
import imageminSvgo from 'imagemin-svgo';
import { deleteAsync } from 'del';

const sass = gulpSass(dartSass);

function taskComplete(message) {
  console.log(message);
}

// Reload only after a watched build has finished writing its output.
function reloadBrowser(done) {
  browserSync.reload();
  done();
}

// Keep WordPress editor pages on Local's native origin. BrowserSync's proxy URL
// rewriting can corrupt escaped block-editor bootstrap data inside admin HTML.
function redirectWordPressAdmin(req, res, next) {
  const acceptsHtml = (req.headers.accept || '').includes('text/html');
  const isAdminRequest = /^\/wp-admin(?:\/|$)/.test(req.url);
  const isLoginRequest = /^\/wp-login\.php(?:\?|$)/.test(req.url);

  if (!acceptsHtml || (!isAdminRequest && !isLoginRequest)) {
    next();
    return;
  }

  const directUrl = new URL(req.url, settings.urlToPreview);
  res.writeHead(302, { Location: directUrl.toString() });
  res.end();
}

async function getWebpack() {
  const { default: webpack } = await import('webpack');
  const { default: webpackStream } = await import('webpack-stream');
  const webpackConfig = await import('./webpack.config.mjs');
  return { webpack, webpackStream, webpackConfig };
}

// Clean scripts task
gulp.task('clean-scripts', () => {
  return deleteAsync([settings.jsBundled]);
});

// Styles task
gulp.task('styles', () => {
  return gulp
    .src(settings.styleSRC)
    .pipe(
      sass({
        errLogToConsole: true,
        outputStyle: settings.outputStyle,
        precision: settings.precision,
      })
    )
    .pipe(gulpAutoprefixer(settings.BROWSERS_LIST))
    .on('error', (error) => console.log(error.toString()))
    .pipe(gulp.dest(settings.styleDestination))
    .on('end', () => taskComplete('Styles task completed'));
});

// Scripts task
gulp.task('scripts', async () => {
  const { webpack, webpackStream, webpackConfig } = await getWebpack();
  return gulp
    .src(settings.jsMain)
    .pipe(webpackStream(webpackConfig.default, webpack))
    .pipe(gulp.dest(settings.jsDestination))
    .on('error', (error) => console.log(error.toString()))
    .on('end', () => taskComplete('Scripts task completed'));
});

// Images task for PNG, JPEG, GIF
gulp.task('images', () => {
  return gulp
    .src([settings.imgSRC, '!**/*.svg'])
    .pipe(
      imagemin([
        imageminGifsicle({ interlaced: true }),
        imageminMozjpeg({ progressive: true }),
        imageminOptipng({ optimizationLevel: 3 }),
      ])
    )
    .pipe(gulp.dest(settings.imgDST))
    .on('end', () => taskComplete('Minified images'));
});

// SVG task
gulp.task('svg', () => {
  return gulp
    .src('assets/img/raw/**/*.svg')
    .pipe(
      imagemin([
        imageminSvgo({
          plugins: [{ removeViewBox: false }, { cleanupIDs: false }],
        }),
      ])
    )
    .pipe(gulp.dest(settings.imgDST))
    .on('end', () => taskComplete('Minified SVGs'));
});

// Watch task
gulp.task('watch', () => {
  browserSync.init({
    notify: false,
    proxy: settings.urlToPreview,
    ghostMode: false,
    middleware: [redirectWordPressAdmin],
    open: 'local',
  });

  gulp.watch(settings.watchPhp, reloadBrowser);
  gulp.watch(settings.watchStyles, gulp.series('styles', reloadBrowser));
  gulp.watch(
    [settings.watchJsModules, settings.watchJsMain],
    gulp.series('clean-scripts', 'scripts', reloadBrowser)
  );
  // Image files in this Local/iCloud setup can emit repeated change events.
  // Run `npm run images` manually when image optimization is needed.
});

// Build current assets before starting the persistent Local development server.
gulp.task('dev', gulp.series('styles', 'clean-scripts', 'scripts', 'watch'));
gulp.task('default', gulp.series('styles', 'clean-scripts', 'scripts', 'images', 'svg', 'watch'));
