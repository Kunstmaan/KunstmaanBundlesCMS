/* eslint-env node */

import gulp from 'gulp';
import { adminBundle } from './groundcontrol/admin-bundle.tasks';
import { dashboardBundle } from './groundcontrol/dashboard-bundle.tasks';
import { mediaBundle } from './groundcontrol/media-bundle.tasks';
import { translatorBundle } from './groundcontrol/translator-bundle.tasks';


// AdminBundle Tasks
const analyzeAdminBundle = gulp.series(
    adminBundle.tasks.eslint,
    adminBundle.tasks.stylelint
);

const buildLocalAdminBundle = gulp.series(
    adminBundle.tasks.copy,
    adminBundle.tasks.cssLocal,
    adminBundle.tasks.scripts
);

const buildOptimizedAdminBundle = gulp.series(
    adminBundle.tasks.copy,
    adminBundle.tasks.cssOptimized,
    adminBundle.tasks.scripts
);

// DashboardBundle Tasks
const analyzeDashboardBundle = gulp.series(
    dashboardBundle.tasks.eslint,
    dashboardBundle.tasks.stylelint
);

const buildLocalDashboardBundle = gulp.series(
    dashboardBundle.tasks.cssLocal,
    dashboardBundle.tasks.scripts
);

const buildOptimizedDashboardBundle = gulp.series(
    dashboardBundle.tasks.cssOptimized,
    dashboardBundle.tasks.scripts
);

// MediaBundle Tasks
const analyzeMediaBundle = gulp.series(
    mediaBundle.tasks.eslint,
);

const buildLocalMediaBundle = gulp.series(
    mediaBundle.tasks.scripts
);

const buildOptimizedMediaBundle = gulp.series(
    mediaBundle.tasks.scripts
);

// TranslatorBundle Tasks
const analyzeTranslatorBundle = gulp.series(
    translatorBundle.tasks.eslint,
    translatorBundle.tasks.stylelint
);

const buildLocalTranslatorBundle = gulp.series(
    translatorBundle.tasks.cssLocal,
    translatorBundle.tasks.scripts
);

const buildOptimizedTranslatorBundle = gulp.series(
    translatorBundle.tasks.cssOptimized,
    translatorBundle.tasks.scripts
);


// Combine bundles
const analyze = gulp.series(
    analyzeAdminBundle,
    analyzeDashboardBundle,
    analyzeMediaBundle,
    analyzeTranslatorBundle
);

const test = gulp.series(
    analyze
);

const buildLocal = gulp.series(
    buildLocalAdminBundle,
    buildLocalDashboardBundle,
    buildLocalMediaBundle,
    buildLocalTranslatorBundle
);

const buildOptimized = gulp.series(
    buildOptimizedAdminBundle,
    buildOptimizedDashboardBundle,
    buildOptimizedMediaBundle,
    buildOptimizedTranslatorBundle
);

const startLocal = gulp.series(
    buildLocal,
    adminBundle.tasks.server,
    buildOnChange
);

const testAndBuildOptimized = gulp.series(
    test,
    buildOptimized
);

// Watches
export function buildOnChange(done) {
    gulp.watch(adminBundle.config.srcPath + 'scss/**/*.scss', adminBundle.tasks.cssLocal);
    gulp.watch(adminBundle.config.srcPath + 'js/**/!(*.spec).js', adminBundle.tasks.scripts);
    gulp.watch(dashboardBundle.config.srcPath + 'scss/**/*.scss', dashboardBundle.tasks.cssLocal);
    gulp.watch(dashboardBundle.config.srcPath + 'js/**/!(*.spec).js', dashboardBundle.tasks.scripts);
    gulp.watch(mediaBundle.config.srcPath + 'js/**/!(*.spec).js', mediaBundle.tasks.scripts);
    gulp.watch(translatorBundle.config.srcPath + 'scss/**/*.scss', translatorBundle.tasks.cssLocal);
    gulp.watch(translatorBundle.config.srcPath + 'js/**/!(*.spec).js', translatorBundle.tasks.scripts);
    done();
}

// Export public tasks
export { test, buildOptimized, testAndBuildOptimized, startLocal };
