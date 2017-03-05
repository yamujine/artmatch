'use strict';

var gulp = require('gulp');
var watch = require('gulp-watch');
var sync = require('gulp-sync')(gulp);
var sass = require('gulp-sass');
var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var clean = require('gulp-clean');
var filesExist = require('files-exist');
var imagemin = require('gulp-imagemin');

gulp.task('dist-clean', function () {
  return gulp.src('static/dist/', {read: false})
    .pipe(clean());
});

/** For Develop TASK START **/
gulp.task('jquery', function () {
  filesExist(['assets/bower_components/jquery/dist']);

  return gulp.src('assets/bower_components/jquery/dist/**')
    .pipe(gulp.dest('static/dist/jquery'));
});

gulp.task('bootstrap', function () {
  filesExist(['assets/bower_components/bootstrap/dist']);

  return gulp.src('assets/bower_components/bootstrap/dist/**')
    .pipe(gulp.dest('static/dist/bootstrap'));
});

gulp.task('prefixfree', function () {
  filesExist(['assets/bower_components/prefixfree']);

  return gulp.src('assets/bower_components/prefixfree/**')
    .pipe(gulp.dest('static/dist/prefixfree'));
});

gulp.task('slick', function () {
  filesExist(['assets/bower_components/slick-carousel/slick']);

  return gulp.src('assets/bower_components/slick-carousel/slick/**')
    .pipe(gulp.dest('static/dist/slick'));
});

gulp.task('magnificpopup', function () {
  filesExist(['assets/bower_components/magnific-popup/dist']);

  return gulp.src('assets/bower_components/magnific-popup/dist/**')
    .pipe(gulp.dest('static/dist/magnific-popup'));
});

gulp.task('magiccheck', function () {
  filesExist(['assets/bower_components/magic-check']);

  return gulp.src('assets/bower_components/magic-check/css/**')
    .pipe(gulp.dest('static/dist/magic-check'));
});

gulp.task('jquery-validation', function () {
  filesExist(['assets/bower_components/jquery-validation']);

  return gulp.src('assets/bower_components/jquery-validation/dist/**')
    .pipe(gulp.dest('static/dist/jquery-validation'));
});

gulp.task('jquery-validation-bootstrap-tooltip', function () {
  filesExist(['assets/bower_components/jquery-validation-bootstrap-tooltip']);

  return gulp.src('assets/bower_components/jquery-validation-bootstrap-tooltip/jquery-validate.bootstrap-tooltip.js')
    .pipe(gulp.dest('static/dist/jquery-validation-bootstrap-tooltip'));
});

gulp.task('extra-assets-js', function () {
  return gulp.src('assets/js/**')
    .pipe(gulp.dest('static/dist/js'));
});

gulp.task('extra-assets-css', function () {
  return gulp.src('assets/css/**')
    .pipe(gulp.dest('static/dist/css'));
});
/** For Develop TASK END **/

gulp.task('uglify', function () {
  return gulp.src(['assets/js/**/*.js'])
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'))
    .pipe(uglify())
    .pipe(gulp.dest('static/dist/js'));
});

gulp.task('sass', function () {
  return gulp.src([
    'assets/scss/**/*.scss',
    '!assets/scss/helper/**/*.scss'
  ])
    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('static/dist/css'));
});

gulp.task('dev', ['jquery', 'bootstrap', 'prefixfree', 'slick', 'magnificpopup', 'magiccheck', 'jquery-validation', 'jquery-validation-bootstrap-tooltip', 'extra-assets-js', 'extra-assets-css']);

gulp.task('font', function () {
  return gulp.src('assets/fonts/**')
    .pipe(gulp.dest('static/dist/fonts'));
});

gulp.task('image', function() {
  return gulp.src('static/images/*')
    .pipe(imagemin())
    .pipe(gulp.dest('static/images'));
});

gulp.task('build', sync.sync([
  'dist-clean',
  [
    'dev',
    'uglify',
    'sass',
    'font',
    'image'
  ]
]));

gulp.task('build:watch', function () {
  gulp.watch('assets/scss/**/*.scss', ['sass']);
  gulp.watch('assets/js/*.js', ['uglify']);
});
