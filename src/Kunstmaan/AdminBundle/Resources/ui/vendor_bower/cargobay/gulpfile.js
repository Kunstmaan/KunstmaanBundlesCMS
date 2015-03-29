'use strict';

// Require Gulp
var gulp = require('gulp');


// Load Gulp plugins
var plugins = require('gulp-load-plugins')();

var chalk = require('chalk');

// Config
var basePath = './src';
var cargobay = {
    scss : basePath + '/**/*/styles/scss/**/*.scss',
    js : [basePath + '/**/*/js/**/*.js', '!' + basePath + '/**/*/js/**/*.min.js'] // ! in front of a path excludes that path/those files. This is to prevent double minification.
};


// Styles
gulp.task('styles', function() {
    return gulp.src(cargobay.scss)
	// Scss -> Css
	.pipe(plugins.rubySass())
	.on('error', function (err) { console.log(err.message); })

	// Combine Media Queries
	.pipe(plugins.combineMediaQueries())

	// Prefix where needed
	.pipe(plugins.autoprefixer('last 1 version'))

	// Use rename function to correctly place the dest path.
	.pipe(plugins.rename(function(path){
	    path.dirname += '/../css';
	}))

	// Write to output dest
	.pipe(gulp.dest('./src/')) // Because of rename dest will be: './src/**/*/styles/css/**/*.css'

	// Rename the file again for the minified version of the css
	.pipe(plugins.rename(function(path){
	    path.basename += '.min';
	}))

	// Minify output
	.pipe(plugins.minifyCss())

	// Write to output dest
	.pipe(gulp.dest('./src/')) // Because of rename dest will be: './src/**/*/styles/css/**/*.min.css'

	// Show total size of css
	.pipe(plugins.size({
	    title: 'css'
	}));
});


// Scripts
gulp.task('scripts', function () {
    return gulp.src(cargobay.js)
	// JsHint
	.pipe(plugins.jshint())
	.pipe(plugins.jshint.reporter(require('jshint-stylish')))

	// Rename file befoure uglify
	.pipe(plugins.rename(function(path){
	    path.basename += '.min';
	}))

	// Uglify
	.pipe(plugins.uglify())

	// Write to output dest
	.pipe(gulp.dest('./src/')) // Because of rename, the dest will be ./src/**/*/js/**/*.min.js

	// Show total size of js
	.pipe(plugins.size({
	    title: 'js'
	}));
});


// Watch
gulp.task('watch', function () {
    // Styles
    gulp.watch(cargobay.scss, ['styles']);

    // Scripts
    gulp.watch(cargobay.js, ['scripts']);

    console.log(chalk.green('Build.complete!'));
});


// Build
gulp.task('build', ['styles', 'scripts'], function(){
    console.log(chalk.green('Build complete!'));
});


// Default
gulp.task('default', ['build'], function () {
    gulp.start('watch');
});
