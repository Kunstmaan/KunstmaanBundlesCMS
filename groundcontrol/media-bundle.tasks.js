/* eslint-env node */

import consoleArguments from './console-arguments';

import createEslintTask from './tasks/eslint';
import createScriptsTask from './tasks/scripts';

export const mediaBundle = {
    config: {
        srcPath: './src/Kunstmaan/MediaBundle/Resources/ui/',
        distPath: './src/Kunstmaan/MediaBundle/Resources/public/',
    },
    tasks: {}
};

mediaBundle.tasks.eslint = createEslintTask({
    src: mediaBundle.config.srcPath + 'js/**/*.js',
    failAfterError: !consoleArguments.continueAfterTestError
});

mediaBundle.tasks.scripts = createScriptsTask({
    src: [
        mediaBundle.config.srcPath + 'vendor_bower/plupload/js/plupload.full.min.js',
        mediaBundle.config.srcPath + 'vendor_bower/picturefill/dist/picturefill.min.js',
        mediaBundle.config.srcPath + 'js/_bulk-upload.js',
        mediaBundle.config.srcPath + 'js/_dnd-upload.js',
        mediaBundle.config.srcPath + 'js/app.js'
    ],
    dest: mediaBundle.config.distPath + 'js',
    filename: 'media-bundle.min.js'
});
