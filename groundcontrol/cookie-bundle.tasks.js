/* eslint-env node */

import consoleArguments from './console-arguments';

import createScriptsTask from './tasks/scripts';
import createBundleTask, { getBabelLoaderOptions } from './tasks/bundle';
import path from 'path';
import { adminBundle } from './admin-bundle.tasks';
import TerserPlugin from 'terser-webpack-plugin';

export const cookieBundle = {
    config: {
        srcPath: './src/Kunstmaan/CookieBundle/Resources/ui/',
        distPath: './src/Kunstmaan/CookieBundle/Resources/public/',
        publicPath: '/bundles/kunstmaancookie'
    },
    tasks: {}
};

cookieBundle.tasks.bundleOptimized = createBundleTask({
    config: {
        mode: 'production',
        entry: `${cookieBundle.config.srcPath}js/index.js`,
        output: {
            filename: 'cookie-bundle.min.js',
            path: path.resolve(__dirname, `.${cookieBundle.config.distPath}js`)
        },
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loader: 'babel-loader',
                    options: getBabelLoaderOptions({
                        optimize: true
                    })
                },
            ],
        },
    },
    logStats: true
});
