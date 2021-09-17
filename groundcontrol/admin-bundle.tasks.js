/* eslint-env node */

import gulp from 'gulp';
import webpack from 'webpack';
import path from 'path';
import TerserPlugin from 'terser-webpack-plugin';


import consoleArguments from './console-arguments';

import createEslintTask from './tasks/eslint';
import createStylelintTask from './tasks/stylelint';
import createCopyTask from './tasks/copy';
import {createCssLocalTask, createCssOptimizedTask} from './tasks/css';
import createScriptsTask from './tasks/scripts';
import createBundleTask, {getBabelLoaderOptions} from './tasks/bundle';

export const adminBundle = {
    config: {
        srcPath: './src/Kunstmaan/AdminBundle/Resources/ui/',
        distPath: './src/Kunstmaan/AdminBundle/Resources/public/',
        publicPath: '/bundles/kunstmaanadmin'
    },
    tasks: {}
};

adminBundle.tasks.eslint = createEslintTask({
    src: `${adminBundle.config.srcPath}jsnext/**/*.js`,
    failAfterError: !consoleArguments.continueAfterTestError
});

adminBundle.tasks.stylelint = createStylelintTask({
    src: `${adminBundle.config.srcPath}scssnext/**/*.scss`,
});

adminBundle.tasks.copy = gulp.parallel(
    createCopyTask({src: [`${adminBundle.config.srcPath}img/**`], dest: `${adminBundle.config.distPath}img`}),
    createCopyTask({src: [`${adminBundle.config.srcPath}icons/**`], dest: `${adminBundle.config.distPath}icons`})
);

adminBundle.tasks.cssLocal = createCssLocalTask({src: `${adminBundle.config.srcPath}scss/*.scss`, dest: `${adminBundle.config.distPath}css`});
adminBundle.tasks.cssNextLocal = createCssLocalTask({src: `${adminBundle.config.srcPath}scssnext/*.scss`, dest: `${adminBundle.config.distPath}cssnext`});



adminBundle.tasks.cssOptimized = createCssOptimizedTask({src: `${adminBundle.config.srcPath}scss/*.scss`, dest: `${adminBundle.config.distPath}css`});
adminBundle.tasks.cssNextOptimized = createCssOptimizedTask({src: `${adminBundle.config.srcPath}scssnext/*.scss`, dest: `${adminBundle.config.distPath}cssnext`});

adminBundle.tasks.scripts = createScriptsTask({
    src: [
        './node_modules/jquery/dist/jquery.js',
        './node_modules/velocity-animate/velocity.js',
        './node_modules/moment/moment.js',
        './node_modules/jstree/dist/jstree.js',
        './node_modules/bootstrap-sass/assets/javascripts/bootstrap.js',
        './node_modules/select2/dist/js/select2.full.js',
        './node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        './node_modules/cargobay/src/toggle/js/jquery.toggle.js',
        './node_modules/cargobay/src/scroll-to-top/js/jquery.scroll-to-top.js',
        './node_modules/sortablejs/Sortable.js',
        './node_modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.js',
        './node_modules/jquery.typewatch/jquery.typewatch.js',
        './node_modules/ckeditor/ckeditor.js',
        './node_modules/ckeditor/adapters/jquery.js',
        `${adminBundle.config.srcPath}js/**/*.js`
    ],
    dest: `${adminBundle.config.distPath}js`,
    filename: 'admin-bundle.min.js'
});

adminBundle.tasks.bundle = createBundleTask({
    config: {
        entry: `${adminBundle.config.srcPath}jsnext/app.js`,
        output: {
            filename: 'admin-bundle.next.js',
            path: path.resolve(__dirname, `.${adminBundle.config.distPath}js`)
        },
        devtool: 'cheap-module-source-map',
        mode: 'development',
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loader: 'babel-loader',
                    options: getBabelLoaderOptions({
                        transpileOnlyForLastChromes: consoleArguments.speedupLocalDevelopment
                    })
                }
            ]
        }
    }
});

adminBundle.tasks.bundleOptimized = createBundleTask({
    config: {
        entry: `${adminBundle.config.srcPath}jsnext/app.js`,
        output: {
            filename: 'admin-bundle.next.js',
            path: path.resolve(__dirname, `.${adminBundle.config.distPath}js`)

        },
        devtool: 'source-map',
        optimization: {
            minimize: true,
            minimizer: [new TerserPlugin({
                extractComments: false,
                terserOptions: {
                    mangle: true
                }
            })]
        },
        mode: 'production',
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loader: 'babel-loader',
                    options: getBabelLoaderOptions({
                        optimize: true
                    })
                }
            ]
        }
    },
    logStats: true
});

adminBundle.tasks.bundlePolyfills = createBundleTask({
    config: {
        entry: ['babel-polyfill', `${adminBundle.config.srcPath}jsnext/polyfills.js`],
        output: {
            filename: 'admin-bundle-polyfills.js',
            path: path.resolve(__dirname, `.${adminBundle.config.distPath}js`)
        },
        devtool: 'source-map',
        optimization: {
            minimize: true
        },
        mode: 'production',
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loader: 'babel-loader',
                    options: getBabelLoaderOptions({
                        optimize: true
                    })
                }
            ]
        }
    },
    logStats: true
});
