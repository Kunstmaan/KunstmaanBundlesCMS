import gulp from 'gulp';
import dartSass from 'sass';
import notifier from 'node-notifier';
import sourcemaps from 'gulp-sourcemaps';
import postcss from 'gulp-postcss';
import rev from 'gulp-rev';
import cssnano from 'cssnano';
import autoprefixer from 'autoprefixer';
import debug from 'gulp-debug';
import gulpSass from 'gulp-sass';
const sass = gulpSass(dartSass);

export function createCssLocalTask({ src = undefined, dest = undefined }) {
    return function cssLocal() {
        return gulp.src(src)
            .pipe(debug({ title: 'Building' }))
            .pipe(sourcemaps.init())
            .pipe(sass().on('error', sassErrorHandler))
            .pipe(postcss([autoprefixer()]))
            .pipe(sourcemaps.write())
            .pipe(gulp.dest(dest));
    };
}

export function createCssOptimizedTask({ src = undefined, dest = undefined, cssnanoConfig = { safe: true } }) {
    return function cssOptimized() {
        return gulp.src(src)
            .pipe(debug({ title: 'Building' }))
            .pipe(sass().on('error', sassErrorHandler))
            .pipe(postcss([autoprefixer(), cssnano(cssnanoConfig)]))
            //.pipe(rev())
            .pipe(gulp.dest(dest));
    };
}

function sassErrorHandler(error) {
    console.log(`Sass Error:\n${error.messageFormatted}`);
    notifier.notify({
        title: 'Sass',
        message: `Error in ${error.relativePath} at L${error.line}:C${error.column}`
    });
    this.emit('end');
}
