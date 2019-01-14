import gulp from 'gulp';
import eslintPlugin from 'gulp-eslint';
import cache from 'gulp-cached';
import path from 'path';
import gulpif from 'gulp-if';

const ESLINT_CACHE = 'eslint';

export default function createEslintTask({
    src = undefined,
    failAfterError = true,
}) {
    return function eslint() {
        return gulp.src(src)
            .pipe(cache(ESLINT_CACHE))
            .pipe(eslintPlugin())
            .pipe(eslintPlugin.format())
            .pipe(eslintPlugin.result(removeInvalidFilesFromCache))
            .pipe(gulpif(failAfterError, eslintPlugin.failAfterError()));
    };
}

function removeInvalidFilesFromCache(result) {
    if (result.warningCount > 0 || result.errorCount > 0) {
        // If a file has errors/warnings remove it from cache
        delete cache.caches[ESLINT_CACHE][path.resolve(result.filePath)];
    }
}
