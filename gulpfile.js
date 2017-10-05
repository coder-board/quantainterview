/**
 * Created by Administrator on 2016/8/15.
 */
var gulp = require('gulp');

//引入组件
var less = require('gulp-less'),
    livereload = require('gulp-livereload'),
    minifycss = require('gulp-minify-css'),
    concat = require('gulp-concat'),		//文件合并
    rename = require('gulp-rename'),		//文件更名

    uglify = require('gulp-uglify'),
    autoprefixer = require('gulp-autoprefixer'),    //前缀自动补全
    cache = require('gulp-cache');

//css
gulp.task('css', function() {
    return gulp.src('src/less/*.less')
        .pipe(less())
        .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'))
        .pipe(rename({suffix: '.min'}))
        .pipe(minifycss())
        .pipe(gulp.dest('dist/css'));
});

// js
gulp.task('js', function() {
    return gulp.src('src/js/*.js')
        .pipe(rename({ suffix: '.min' }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/js'));
});

// Default task
gulp.task('default', function() {
    gulp.start('css', 'js');
});

gulp.task('watch', function() {
    livereload.listen();
    gulp.watch('src/less/*.less', ['css']);
    gulp.watch('src/js/*.js', ['js']);
    gulp.watch('dist/css/*.*',function(file){
        livereload.changed(file.path);
    })
});