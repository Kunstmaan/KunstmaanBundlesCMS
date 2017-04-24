/* eslint-env node */

import consoleArguments from './console-arguments';

import createEslintTask from './tasks/eslint';
import createStylelintTask from './tasks/stylelint';
import {createCssLocalTask, createCssOptimizedTask} from './tasks/css';
import createScriptsTask from './tasks/scripts';

export const translatorBundle = {
    config: {
        srcPath: './src/Kunstmaan/TranslatorBundle/Resources/ui/',
        distPath: './src/Kunstmaan/TranslatorBundle/Resources/public/',
    },
    tasks: {}
};

translatorBundle.tasks.stylelint = createStylelintTask({src: translatorBundle.config.srcPath + 'scss/**/*.scss'});

translatorBundle.tasks.cssLocal = createCssLocalTask({src: translatorBundle.config.srcPath + 'scss/style.scss', dest: translatorBundle.config.distPath + 'css'});

translatorBundle.tasks.cssOptimized = createCssOptimizedTask({src: translatorBundle.config.srcPath + 'scss/*.scss', dest: translatorBundle.config.distPath + 'css'});

translatorBundle.tasks.eslint = createEslintTask({
    src: translatorBundle.config.srcPath + 'js/**/*.js',
    failAfterError: !consoleArguments.continueAfterTestError
});

translatorBundle.tasks.scripts = createScriptsTask({
    src: [
        translatorBundle.config.srcPath + 'vendor_bower/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js',
        translatorBundle.config.srcPath + 'js/_inline-edit.js',
        translatorBundle.config.srcPath + 'js/app.js'
    ],
    dest: translatorBundle.config.distPath + 'js',
    filename: 'translator-bundle.min.js'
});
