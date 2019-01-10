import gulp from 'gulp';
import injectPlugin from 'gulp-inject';

export default function createInjectTask({
    src = undefined,
    dest = undefined,
    injectables = undefined,
}) {
    return function inject() {
        let stream = gulp.src(src);
        injectables.forEach((injectable) => {
            stream = stream.pipe(injectPlugin(gulp.src(injectable.stream), injectable.config));
        });
        return stream.pipe(gulp.dest(dest));
    };
}
