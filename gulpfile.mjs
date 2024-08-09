import gulp from 'gulp';
import gulpSass from 'gulp-sass';
import * as dartSass from 'sass';
import gulpAutoprefixer from 'gulp-autoprefixer';
import browserSync from 'browser-sync';
import notify from 'gulp-notify';
import settings from './settings.mjs';
import imagemin from 'gulp-imagemin';
import imageminGifsicle from 'imagemin-gifsicle';
import imageminMozjpeg from 'imagemin-mozjpeg';
import imageminOptipng from 'imagemin-optipng';
import imageminSvgo from 'imagemin-svgo';
import { deleteAsync } from 'del';

const sass = gulpSass(dartSass);

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
    .pipe(
      notify({
        message: '✅ Styles task completed',
        onLast: true,
      })
    );
});

// Scripts task
gulp.task('scripts', async (callback) => {
  const { webpack, webpackStream, webpackConfig } = await getWebpack();
  return gulp
    .src(settings.jsMain)
    .pipe(webpackStream(webpackConfig.default, webpack))
    .pipe(gulp.dest(settings.jsBundled))
    .on('error', (error) => console.log(error.toString()))
    .pipe(
      notify({
        message: '✅ Scripts task completed',
        onLast: true,
      })
    );
  callback();
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
    .pipe(
      notify({
        message: '✅ Minified images',
        onLast: true,
      })
    );
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
    .pipe(
      notify({
        message: '✅ Minified SVGs',
        onLast: true,
      })
    );
});

// Watch task
gulp.task('watch', () => {
  browserSync.init({
    notify: false,
    proxy: settings.urlToPreview,
    ghostMode: false,
  });

  gulp.watch(settings.watchPhp, (done) => {
    browserSync.reload();
    done();
  });
  gulp.watch(settings.watchStyles, gulp.series('styles')).on('change', browserSync.reload);
  gulp.watch([settings.watchJsModules, settings.watchJsMain], gulp.series('clean-scripts', 'scripts')).on('change', browserSync.reload);
  gulp.watch([settings.imgSRC, '!**/*.svg'], gulp.series('images')).on('change', browserSync.reload);
  gulp.watch('assets/img/raw/**/*.svg', gulp.series('svg')).on('change', browserSync.reload);
});

gulp.task('default', gulp.series('styles', 'clean-scripts', 'scripts', 'images', 'svg', 'watch'));