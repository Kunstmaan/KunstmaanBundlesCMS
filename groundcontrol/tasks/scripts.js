import gulp from 'gulp';
import uglify from 'gulp-uglify';
import concat from 'gulp-concat';
import debug from 'gulp-debug';
import { noop } from 'gulp-util';

/**
 * Temporary scripts task.
 * This should be replaced by webpack to bundle the files
 */

export default function createScriptsTask({
    src = undefined,
    dest = undefined,
    filename = undefined,
    uglifyJs = true,
}) {
    return function scriptsLocal() {
        return gulp
            .src(src)
            .pipe(debug({ title: 'Building' }))
            .pipe(uglifyJs
                ? uglify({
                    mangle: {
                        except: ['jQuery'],
                    },
                })
                : noop())
            .pipe(concat(filename))
            .pipe(gulp.dest(dest));
    };
}
