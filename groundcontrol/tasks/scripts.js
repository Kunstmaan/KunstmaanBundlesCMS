import gulp from 'gulp';
import uglify from 'gulp-uglify';
import concat from 'gulp-concat';

/**
 * Temporary scripts task.
 * This should be replaced by webpack to bundle the files
 */

export default function createScriptsTask({src = undefined, dest = undefined, filename = undefined}) {
    return function scriptsLocal() {
        return gulp.src(src)
            .pipe(uglify({
                mangle: {
                    except: ['jQuery']
                }
            }))
            .pipe(concat(filename))
            .pipe(gulp.dest(dest));
    };
}
