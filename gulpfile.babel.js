/* eslint-env node */

import gulp from 'gulp';
import {adminBundle} from './groundcontrol/admin-bundle.tasks';
import {dashboardBundle} from './groundcontrol/dashboard-bundle.tasks';
import {mediaBundle} from './groundcontrol/media-bundle.tasks';
import {translatorBundle} from './groundcontrol/translator-bundle.tasks';
import startLocalTask, { buildOnChange, testOnChange } from './groundcontrol/start-local.task';
import createBuildGroundControlSkeletonTask from './groundcontrol/tasks/build-gc-skeleton';


// AdminBundle Tasks
const analyzeAdminBundle = gulp.series(
    adminBundle.tasks.eslint,
    adminBundle.tasks.stylelint,
);

const buildLocalAdminBundle = gulp.series(
    adminBundle.tasks.copy,
    adminBundle.tasks.cssLocal,
    adminBundle.tasks.cssNextLocal,
    adminBundle.tasks.scripts,
    adminBundle.tasks.bundle
);

const buildOptimizedAdminBundle = gulp.series(
    adminBundle.tasks.copy,
    adminBundle.tasks.cssOptimized,
    adminBundle.tasks.cssNextOptimized,
    adminBundle.tasks.scripts,
    adminBundle.tasks.bundlePolyfills,
    adminBundle.tasks.bundleOptimized
);

// DashboardBundle Tasks
const buildLocalDashboardBundle = gulp.series(
    dashboardBundle.tasks.cssLocal,
    dashboardBundle.tasks.scripts,
    dashboardBundle.tasks.scriptsSetup
);

const buildOptimizedDashboardBundle = gulp.series(
    dashboardBundle.tasks.cssOptimized,
    dashboardBundle.tasks.scripts,
    dashboardBundle.tasks.scriptsSetup
);

// MediaBundle Tasks
const buildLocalMediaBundle = gulp.series(
    mediaBundle.tasks.scripts
);

const buildOptimizedMediaBundle = gulp.series(
    mediaBundle.tasks.scripts
);

// TranslatorBundle Tasks
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
    analyzeAdminBundle
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

const testAndBuildOptimized = gulp.series(
    test,
    buildOptimized
);

// Setting up server, local dev
const startLocal = gulp.series(
    buildLocal,
    startLocalTask,
    analyze,
    buildOnChange,
    testOnChange,
);

// Development sepcific tasks
const buildGroundControlSkeleton = gulp.series(createBuildGroundControlSkeletonTask('./src/Kunstmaan/GeneratorBundle/Resources/SensioGeneratorBundle/skeleton/layout/groundcontrol'));

// Export public tasks
export {test, buildOptimized, testAndBuildOptimized, startLocal, buildGroundControlSkeleton};
