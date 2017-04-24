/* eslint-env node */

import gulp from 'gulp';
// import webpack from 'webpack';

import consoleArguments from './console-arguments';

import createEslintTask from './tasks/eslint';
import createStylelintTask from './tasks/stylelint';
import createCopyTask from './tasks/copy';
import {createCssLocalTask, createCssOptimizedTask} from './tasks/css';
import createScriptsTask from './tasks/scripts';
// import createBundleTask from './tasks/bundle';
import createServerTask from './tasks/server';

// import {getBabelLoaderOptions} from './configured-tasks';

export const adminBundle = {
    config: {
        srcPath: './src/Kunstmaan/AdminBundle/Resources/ui/',
        distPath: './src/Kunstmaan/AdminBundle/Resources/public/',
    },
    tasks: {}
};

adminBundle.tasks.eslint = createEslintTask({
    src: adminBundle.config.srcPath + 'js/**/*.js',
    failAfterError: !consoleArguments.continueAfterTestError
});

adminBundle.tasks.stylelint = createStylelintTask({src: adminBundle.config.srcPath + 'scss/**/*.scss'});

// export const clean = createCleanTask({target: [adminBundle.config.distPath]});

adminBundle.tasks.copy = gulp.parallel(
    createCopyTask({src: [adminBundle.config.srcPath + 'img/**'], dest: adminBundle.config.distPath + 'img'})
);

adminBundle.tasks.cssLocal = createCssLocalTask({src: adminBundle.config.srcPath + 'scss/style.scss', dest: adminBundle.config.distPath + 'css'});

adminBundle.tasks.cssOptimized = createCssOptimizedTask({src: adminBundle.config.srcPath + 'scss/*.scss', dest: adminBundle.config.distPath + 'css'});

// adminBundle.tasks.bundleLocal = createBundleTask({
//     config: {
//         entry: adminBundle.config.srcPath + 'js/app.js',
//         output: {
//             filename: adminBundle.config.distPath + 'js/admin-bundle.js'
//         },
//         devtool: 'cheap-module-source-map',
//         module: {
//             rules: [
//                 {
//                     test: /\.js$/,
//                     exclude: /node_modules/,
//                     loader: 'babel-loader',
//                     query: getBabelLoaderOptions({
//                         transpileOnlyForLastChromes: consoleArguments.speedupLocalDevelopment
//                     })
//                 },
//                 {
//                     test: /\/cargobay\/.+\.scroll-to-top\.js/,
//                     use: 'exports-loader?cargobay.scrollToTop'
//                 },
//                 {
//                     test: /\/cargobay\/.+\.toggle\.js/,
//                     use: 'exports-loader?cargobay.toggle'
//                 }
//             ]
//         },
//         plugins: [
//             new webpack.ProvidePlugin({
//                 $: 'jquery',
//                 jQuery: 'jquery',
//                 'window.jQuery': 'jquery'
//             })
//         ]
//     }
// });
//
// adminBundle.tasks.bundleOptimized = createBundleTask({
//     config: {
//         entry: adminBundle.config.srcPath + 'js/app.js',
//         output: {
//             filename: adminBundle.config.distPath + 'js/admin-bundle.js'
//         },
//         module: {
//             rules: [
//                 {
//                     test: /\.js$/,
//                     exclude: /node_modules/,
//                     loader: 'babel-loader',
//                     query: getBabelLoaderOptions({
//                         optimize: true
//                     })
//                 },
//                 {
//                     test: /\/cargobay\/.+\.scroll-to-top\.js/,
//                     use: 'exports-loader?cargobay.scrollToTop'
//                 },
//                 {
//                     test: /\/cargobay\/.+\.toggle\.js/,
//                     use: 'exports-loader?cargobay.toggle'
//                 }
//             ]
//         },
//         plugins: [
//             new webpack.optimize.UglifyJsPlugin({mangle: true, sourceMap: true}),
//             new webpack.ProvidePlugin({
//                 $: 'jquery',
//                 jQuery: 'jquery',
//                 'window.jQuery': 'jquery'
//             })
//         ]
//     },
//     logStats: true
// });

adminBundle.tasks.server = createServerTask({
    config: {
        ui: false,
        ghostMode: false,
        files: [
            adminBundle.config.distPath + 'css/*.css',
            adminBundle.config.distPath + 'js/*.js'
        ],
        open: false,
        reloadOnRestart: true,
        notify: true
    }
});

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
        './src/Kunstmaan/AdminBundle/Resources/public/default-theme/ckeditor/ckeditor.js',
        './src/Kunstmaan/AdminBundle/Resources/public/default-theme/ckeditor/adapters/jquery.js',
        adminBundle.config.srcPath + 'js/**/*.js'
    ],
    dest: adminBundle.config.distPath + 'js',
    filename: 'admin-bundle.min.js'
});