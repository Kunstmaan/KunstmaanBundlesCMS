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
        publicPath: '/bundles/kunstmaantranslator'
    },
    tasks: {}
};

translatorBundle.tasks.cssLocal = createCssLocalTask({src: translatorBundle.config.srcPath + 'scss/*.scss', dest: translatorBundle.config.distPath + 'css'});

translatorBundle.tasks.cssOptimized = createCssOptimizedTask({src: translatorBundle.config.srcPath + 'scss/*.scss', dest: translatorBundle.config.distPath + 'css'});

translatorBundle.tasks.scripts = createScriptsTask({
    src: [
        './node_modules/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js',
        translatorBundle.config.srcPath + 'js/_inline-edit.js',
        translatorBundle.config.srcPath + 'js/app.js'
    ],
    dest: translatorBundle.config.distPath + 'js',
    filename: 'translator-bundle.min.js'
});
