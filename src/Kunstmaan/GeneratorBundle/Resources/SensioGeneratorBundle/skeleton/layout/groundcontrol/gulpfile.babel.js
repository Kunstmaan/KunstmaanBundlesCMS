/* eslint-env node */

import gulp from 'gulp';
import chug from 'gulp-chug';

import {
    eslint,
    stylelint,
    clean,
    copy,
    cssLocal,
    cssOptimized,
    bundleLocal,
    bundleOptimized,
    hologram,
    server,
    buildOnChange,
    testOnChange
} from './groundcontrol/configured-tasks';

const analyze = gulp.series(
    eslint,
    stylelint
);

const test = gulp.series(
    analyze
);

const buildLocal = gulp.series(
    clean,
    copy,
    cssLocal,
    bundleLocal,
    hologram
);

const buildOptimized = gulp.series(
    clean,
    copy,
    cssOptimized,
    bundleOptimized,
    hologram
);

const testAndBuildOptimized = gulp.series(
    test,
    buildOptimized
);

const startLocal = gulp.series(
    analyze,
    buildLocal,
    server,
    buildOnChange,
    testOnChange
);

const startOptimized = gulp.series(
    analyze,
    buildOptimized,
    server
);

const buildCmsAssets = gulp.series(
    () => {
        return gulp.src('vendor/kunstmaan/bundles-cms/gulpfile.babel.js', { read: false })
            .pipe(chug({
                args: [
                    '--rootPath',
                    '../../../../../../../web/assets/'
                ],
                tasks: ['buildOptimized']
            }));
    }
);

export {test, buildOptimized, testAndBuildOptimized, startLocal, startOptimized, buildCmsAssets};
