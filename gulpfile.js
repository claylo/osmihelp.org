var gulp = require('gulp');
var less = require('gulp-less');
var path = require('path');
var cssnano = require('gulp-cssnano');

gulp.task('default', function() {
    // place code for your default task here
});

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

gulp.task('watch-less', ['less'], function() {
    var watcher = gulp.watch('./public/**/*.less', ['less']);
    watcher.on('change', function (event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
});
