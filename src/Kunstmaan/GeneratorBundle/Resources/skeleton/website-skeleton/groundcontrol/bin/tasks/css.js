import gulp from 'gulp';
import sass from 'gulp-sass';
import notifier from 'node-notifier';
import sourcemaps from 'gulp-sourcemaps';
import postcss from 'gulp-postcss';
import cssnano from 'cssnano';
import autoprefixer from 'autoprefixer';

export function createCssLocalTask({
    src = undefined,
    dest = undefined,
}) {
    return function cssLocal() {
        return gulp.src(src)
            .pipe(sourcemaps.init())
            .pipe(sass().on('error', sassErrorHandler))
            .pipe(postcss([autoprefixer()]))
            .pipe(sourcemaps.write())
            .pipe(gulp.dest(dest));
    };
}

export function createCssOptimizedTask({
    src = undefined,
    dest = undefined,
    cssnanoConfig = {
        safe: true,
    },
}) {
    return function cssOptimized() {
        return gulp.src(src)
            .pipe(sass().on('error', sassErrorHandler))
            .pipe(postcss([autoprefixer(), cssnano(cssnanoConfig)]))
            .pipe(gulp.dest(dest));
    };
}

function sassErrorHandler(error) {
    console.log(`Sass Error:\n${error.messageFormatted}`);
    notifier.notify({
        title: 'Sass',
        message: `Error in ${error.relativePath} at L${error.line}:C${error.column}`,
    });
    this.emit('end');
}
