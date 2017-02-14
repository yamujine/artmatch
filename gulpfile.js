'use strict';

var gulp = require('gulp');
var watch = require('gulp-watch');
var sync = require('gulp-sync')(gulp);
var sass = require('gulp-sass');
var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var clean = require('gulp-clean');

gulp.task('dist-clean', function () {
  return gulp.src('static/dist/', {read: false})
    .pipe(clean());
});

/** For Develop TASK START **/
gulp.task('jquery', function () {
  return gulp.src(['assets/bower_components/jquery/dist/**'])
    .pipe(gulp.dest('static/dist/jquery'));
});

gulp.task('bootstrap', function () {
  return gulp.src('assets/bower_components/bootstrap/dist/**')
    .pipe(gulp.dest('static/dist/bootstrap'));
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

gulp.task('font', function () {
  return gulp.src('assets/fonts/**')
    .pipe(gulp.dest('static/dist/fonts'));
});

gulp.task('build', sync.sync([
  'dist-clean',
  [
    'jquery',
    'bootstrap',
    'uglify',
    'sass',
    'font'
  ]
]));

gulp.task('build:watch', function () {
  gulp.watch('assets/scss/**/*.scss', ['sass']);
  gulp.watch('assets/js/**/*.js', ['uglify']);
});
