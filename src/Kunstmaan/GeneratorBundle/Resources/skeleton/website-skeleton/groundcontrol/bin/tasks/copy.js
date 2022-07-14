import gulp from 'gulp';

export default function createCopyTask({
    src = undefined,
    dest = undefined,
}) {
    return function copy() {
        return gulp.src(src)
            .pipe(gulp.dest(dest));
    };
}
