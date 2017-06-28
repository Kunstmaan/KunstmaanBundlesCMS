/* eslint-env node */

import consoleArguments from './console-arguments';

import createEslintTask from './tasks/eslint';
import createStylelintTask from './tasks/stylelint';
import {createCssLocalTask, createCssOptimizedTask} from './tasks/css';
import createScriptsTask from './tasks/scripts';

export const dashboardBundle = {
    config: {
        srcPath: './src/Kunstmaan/DashboardBundle/Resources/ui/',
        distPath: './src/Kunstmaan/DashboardBundle/Resources/public/',
    },
    tasks: {}
};

dashboardBundle.tasks.eslint = createEslintTask({
    src: dashboardBundle.config.srcPath + 'js/**/*.js',
    failAfterError: !consoleArguments.continueAfterTestError
});

dashboardBundle.tasks.stylelint = createStylelintTask({src: dashboardBundle.config.srcPath + 'scss/**/*.scss'});

dashboardBundle.tasks.cssLocal = createCssLocalTask({src: dashboardBundle.config.srcPath + 'scss/style.scss', dest: dashboardBundle.config.distPath + 'css'});

dashboardBundle.tasks.cssOptimized = createCssOptimizedTask({src: dashboardBundle.config.srcPath + 'scss/*.scss', dest: dashboardBundle.config.distPath + 'css'});


dashboardBundle.tasks.scripts = createScriptsTask({
    src: [
        dashboardBundle.config.srcPath + 'js/analytics/metrics.js',
        dashboardBundle.config.srcPath + 'js/analytics/tabs.js',
        dashboardBundle.config.srcPath + 'js/analytics/goals.js',
        dashboardBundle.config.srcPath + 'js/libs/morris-0.5.0.js',
        dashboardBundle.config.srcPath + 'js/libs/raphael-2.1.2.js'
    ],
    dest: dashboardBundle.config.distPath + 'js',
    filename: 'dashboard-bundle.min.js'
});

dashboardBundle.tasks.scriptsSetup = createScriptsTask({
    src: [
        dashboardBundle.config.srcPath + 'js/setup/setup.js'
    ],
    dest: dashboardBundle.config.distPath + 'js',
    filename: 'dashboard-bundle-setup.min.js'
});