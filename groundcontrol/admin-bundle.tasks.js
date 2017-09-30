/* eslint-env node */

import gulp from 'gulp';
import webpack from 'webpack';

import consoleArguments from './console-arguments';

import createEslintTask from './tasks/eslint';
import createStylelintTask from './tasks/stylelint';
import createCopyTask from './tasks/copy';
import {createCssLocalTask, createCssOptimizedTask} from './tasks/css';
import createScriptsTask from './tasks/scripts';
import createServerTask from './tasks/server';
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
    src: adminBundle.config.srcPath + 'js/**/*.js',
    failAfterError: !consoleArguments.continueAfterTestError
});

adminBundle.tasks.stylelint = createStylelintTask({src: adminBundle.config.srcPath + 'scss/**/*.scss'});

adminBundle.tasks.copy = gulp.parallel(
    createCopyTask({src: [adminBundle.config.srcPath + 'img/**'], dest: adminBundle.config.distPath + 'img'})
);

adminBundle.tasks.cssLocal = createCssLocalTask({src: adminBundle.config.srcPath + 'scss/*.scss', dest: adminBundle.config.distPath + 'css'});

adminBundle.tasks.cssOptimized = createCssOptimizedTask({src: adminBundle.config.srcPath + 'scss/*.scss', dest: adminBundle.config.distPath + 'css'});

adminBundle.tasks.scripts = createScriptsTask({
    src: [
        adminBundle.config.srcPath + 'vendor_bower/jquery/dist/jquery.js',
        adminBundle.config.srcPath + 'vendor_bower/velocity/velocity.js',
        adminBundle.config.srcPath + 'vendor_bower/moment/moment.js',
        adminBundle.config.srcPath + 'vendor_bower/jstree/dist/jstree.js',
        adminBundle.config.srcPath + 'vendor_bower/bootstrap-sass-official/assets/javascripts/bootstrap.js',
        adminBundle.config.srcPath + 'vendor_bower/select2/dist/js/select2.full.js',
        adminBundle.config.srcPath + 'vendor_bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        adminBundle.config.srcPath + 'vendor_bower/cargobay/src/toggle/js/jquery.toggle.js',
        adminBundle.config.srcPath + 'vendor_bower/cargobay/src/scroll-to-top/js/jquery.scroll-to-top.js',
        adminBundle.config.srcPath + 'vendor_bower/Sortable/Sortable.js',
        adminBundle.config.srcPath + 'vendor_bower/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js',
        adminBundle.config.srcPath + 'vendor_bower/jquery-typewatch/jquery.typewatch.js',
        './src/Kunstmaan/AdminBundle/Resources/public/default-theme/ckeditor/ckeditor.js',
        './src/Kunstmaan/AdminBundle/Resources/public/default-theme/ckeditor/adapters/jquery.js',
        adminBundle.config.srcPath + 'js/**/*.js'
    ],
    dest: adminBundle.config.distPath + 'js',
    filename: 'admin-bundle.min.js'
});

adminBundle.tasks.bundle = createBundleTask({
    config: {
        entry: adminBundle.config.srcPath + 'jsnext/app.js',
        output: {
            filename: adminBundle.config.distPath + 'js/admin-bundle.next.js',
        },
        devtool: 'cheap-module-source-map',
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loader: 'babel-loader',
                    query: getBabelLoaderOptions({
                        transpileOnlyForLastChromes: consoleArguments.speedupLocalDevelopment
                    })
                }
            ]
        }
    }
});

adminBundle.tasks.bundleOptimized = createBundleTask({
    config: {
        // TODO Create a new polyfill task and only load the bundle when the browser needs it. This adds about 90KB minified extra code..
        entry: ['babel-polyfill', adminBundle.config.srcPath + 'jsnext/app.js'],
        output: {
            filename: adminBundle.config.distPath + 'js/admin-bundle.next.js',
        },
        devtool: 'source-map',
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loader: 'babel-loader',
                    query: getBabelLoaderOptions({
                        optimize: true
                    })
                }
            ]
        },
        plugins: [
            new webpack.optimize.UglifyJsPlugin({
                mangle: true,
                sourceMap: true,
                output: {
                    comments: false
                }
            })
        ]
    },
    logStats: true
});
