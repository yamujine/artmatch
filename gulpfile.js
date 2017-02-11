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
  return gulp.src('dist/', {read: false})
      .pipe(clean());
});

gulp.task('jquery', function () {
  return gulp.src(['assets/bower_components/jquery/dist/jquery.min.js'])
      .pipe(gulp.dest('static/dist/js'));
});

gulp.task('semantic-ui', function () {
  gulp.src(['assets/bower_components/semantic/dist/semantic.min.js'])
      .pipe(gulp.dest('static/dist/js'));

  return gulp.src(['assets/bower_components/semantic/dist/semantic.min.css'])
      .pipe(gulp.dest('static/dist/css'));
});

gulp.task('uglify', function () {
  return gulp.src(['assets/js/**/*.js'])
      .pipe(jshint())
      .pipe(jshint.reporter('jshint-stylish'))
      .pipe(uglify())
      .pipe(gulp.dest('static/dist/js'));
});

gulp.task('sass', function () {
  return gulp.src([
    'assets/scss/**/*.scss'
  ])
      .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
      .pipe(rename({suffix: '.min'}))
      .pipe(gulp.dest('static/dist/css'));
});

gulp.task('build', sync.sync([
  'dist-clean',
  [
    'jquery',
    'semantic-ui',
    'uglify',
    'sass'
  ]
]));

gulp.task('build:watch', function () {
  gulp.watch('static/assets/scss/**/*.scss', ['sass']);
  gulp.watch('static/assets/js/**/*.js', ['uglify']);
});
