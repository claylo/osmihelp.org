var gulp = require('gulp');
var less = require('gulp-less');
var path = require('path');
var cssnano = require('gulp-cssnano');

gulp.task('default', function() {
    // place code for your default task here
});

gulp.task('build', ['twig', 'less']);

gulp.task('less', function () {
    return gulp.src('./public/less/style.less')
        .pipe(less())
        .pipe(gulp.dest('./public/css'));
});

gulp.task('mincss', function () {
    return gulp.src('./public/less/style.less')
        .pipe(less())
        .pipe(cssnano())
        .pipe(gulp.dest('./public/css'));
});

gulp.task('twig', function () {
    var twig = require('gulp-twig');
    return gulp.src('./templates/pages/**/*.twig')
        .pipe(twig({
            'base': './templates/',
            'cache': 'false'
        }))
        .pipe(gulp.dest('./public/'));
});

gulp.task('watch-less', ['less'], function() {
    var watcher = gulp.watch('./public/**/*.less', ['less']);
    watcher.on('change', function (event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
});

gulp.task('watch-twig', ['twig'], function() {
    var watcher = gulp.watch('./templates/**/*.twig', ['twig']);
    watcher.on('change', function (event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
});

gulp.task('watch', ['watch-twig','watch-less']);
