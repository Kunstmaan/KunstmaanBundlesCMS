import gulp from 'gulp';
import chug from 'gulp-chug';

import {
    images,
    eslint,
    stylelint,
    clean,
    copy,
    cssLocal,
    cssOptimized,
    bundleLocal,
    bundleOptimized,
    bundleAdminExtraLocal,
    bundleAdminExtraOptimized,
    generateStyleguide,
    cssStyleguideOptimized,
    bundleStyleguideOptimized,
    copyStyleguide,
    server,
    buildOnChange,
    testOnChange,
} from './groundcontrol/configured-tasks';

const analyze = gulp.series(
    eslint,
    stylelint,
);

const test = gulp.series(analyze);

const buildStyleguide = gulp.series(
    cssStyleguideOptimized,
    bundleStyleguideOptimized,
    generateStyleguide,
    copyStyleguide,
);

const buildLocal = gulp.series(
    clean,
    images,
    copy,
    cssLocal,
    bundleLocal,
    bundleAdminExtraLocal,
    buildStyleguide,
);

const buildOptimized = gulp.series(
    clean,
    images,
    copy,
    cssOptimized,
    bundleOptimized,
    bundleAdminExtraOptimized,
    buildStyleguide,
);

const testAndBuildOptimized = gulp.series(
    test,
    buildOptimized,
);

const startLocal = gulp.series(
    analyze,
    buildLocal,
    server,
    buildOnChange,
    testOnChange,
);

const startOptimized = gulp.series(
    analyze,
    buildOptimized,
    server,
);

const buildCmsAssets = gulp.series(() => gulp.src('vendor/kunstmaan/bundles-cms/gulpfile.babel.js', { read: false })
    .pipe(chug({
        args: [
            '--rootPath',
            '../../../../../../../web/assets/',
        ],
        tasks: ['buildOptimized'],
    })));

export { test, buildOptimized, testAndBuildOptimized, startLocal, startOptimized, buildCmsAssets };
