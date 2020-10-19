import gulp from 'gulp';

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

export { test, buildOptimized, testAndBuildOptimized, startLocal, startOptimized };
