/* eslint-env node */

import consoleArguments from './console-arguments';

import createEslintTask from './tasks/eslint';
import createScriptsTask from './tasks/scripts';

export const mediaBundle = {
    config: {
        srcPath: './src/Kunstmaan/MediaBundle/Resources/ui/',
        distPath: './src/Kunstmaan/MediaBundle/Resources/public/',
        publicPath: '/bundles/kunstmaanmedia'
    },
    tasks: {}
};

mediaBundle.tasks.scripts = createScriptsTask({
    src: [
        './node_modules/plupload/js/plupload.full.min.js',
        './node_modules/picturefill/dist/picturefill.min.js',
        mediaBundle.config.srcPath + 'js/_bulk-upload.js',
        mediaBundle.config.srcPath + 'js/_bulk-move.js',
        mediaBundle.config.srcPath + 'js/_dnd-upload.js',
        mediaBundle.config.srcPath + 'js/app.js'
    ],
    dest: mediaBundle.config.distPath + 'js',
    filename: 'media-bundle.min.js'
});
