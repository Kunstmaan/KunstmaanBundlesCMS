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
    server,
    buildOnChange,
    testOnChange,
} from './groundcontrol/configured-tasks';

const analyze = gulp.series(
    eslint,
    stylelint,
);

const test = gulp.series(analyze);

const buildLocal = gulp.series(
    clean,
    images,
    copy,
    cssLocal,
    bundleLocal,
    bundleAdminExtraLocal,
);

const buildOptimized = gulp.series(
    clean,
    images,
    copy,
    cssOptimized,
    bundleOptimized,
    bundleAdminExtraOptimized,
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
