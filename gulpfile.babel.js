/* eslint-env node */

import gulp from 'gulp';
import { adminBundle } from './groundcontrol/admin-bundle.tasks';
import { dashboardBundle } from './groundcontrol/dashboard-bundle.tasks';
import { mediaBundle } from './groundcontrol/media-bundle.tasks';
import { translatorBundle } from './groundcontrol/translator-bundle.tasks';
import startLocalTask, { buildOnChange } from './groundcontrol/start-local.task';

// AdminBundle Tasks
const analyzeAdminBundle = gulp.series(
    adminBundle.tasks.eslint,
    adminBundle.tasks.stylelint
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

const buildOptimizedDashboardBundle = gulp.series(
    dashboardBundle.tasks.cssOptimized,
    dashboardBundle.tasks.scripts
);

// MediaBundle Tasks
const analyzeMediaBundle = gulp.series(
    mediaBundle.tasks.eslint,
);

const buildOptimizedMediaBundle = gulp.series(
    mediaBundle.tasks.scripts
);

// TranslatorBundle Tasks
const analyzeTranslatorBundle = gulp.series(
    translatorBundle.tasks.eslint,
    translatorBundle.tasks.stylelint
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

const buildOptimized = gulp.series(
    buildOptimizedAdminBundle,
    buildOptimizedDashboardBundle,
    buildOptimizedMediaBundle,
    buildOptimizedTranslatorBundle
);

const testAndBuildOptimized = gulp.series(
    test,
    buildOptimized
);

// Setting up server, local dev
const startLocal = gulp.series(
    buildOptimized,
    startLocalTask,
    buildOnChange
);

// Export public tasks
export { test, buildOptimized, testAndBuildOptimized, startLocal };