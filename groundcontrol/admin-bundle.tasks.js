/* eslint-env node */

import gulp from 'gulp';
import critical from 'critical';
import http from 'http';
import fs from 'fs';
import url from 'url';
import path from 'path';
// import webpack from 'webpack';

import consoleArguments from './console-arguments';

import createEslintTask from './tasks/eslint';
import createStylelintTask from './tasks/stylelint';
import createCopyTask from './tasks/copy';
import { createCssLocalTask, createCssOptimizedTask } from './tasks/css';
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

adminBundle.tasks.stylelint = createStylelintTask({ src: adminBundle.config.srcPath + 'scss/**/*.scss' });

// export const clean = createCleanTask({target: [adminBundle.config.distPath]});

adminBundle.tasks.copy = gulp.parallel(
    createCopyTask({ src: [adminBundle.config.srcPath + 'img/**'], dest: adminBundle.config.distPath + 'img' })
);

adminBundle.tasks.cssLocal = createCssLocalTask({ src: adminBundle.config.srcPath + 'scss/*.scss', dest: adminBundle.config.distPath + 'css' });

adminBundle.tasks.cssOptimized = createCssOptimizedTask({ src: adminBundle.config.srcPath + 'scss/*.scss', dest: adminBundle.config.distPath + 'css' });

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

const extractCriticalCss = (urlToCheck, done) => {
    const distPath = adminBundle.config.distPath;
    const CSS_REGEX = /(<link\s+rel=\"stylesheet\"\s+href=\").*(css\/.*\.css.*\"\s?(>|\/>))/gmi;

    http.get(urlToCheck, res => {
        const data = [];
        res.on('data', function (chunk) {
            const originalData = chunk.toString();
            const data = originalData.replace(CSS_REGEX, `$1${distPath}$2`);

            let cssFiles = [];
            let originalCssSize = 0;
            // Rewrite the url to the css bundles dist path
            const cssTags = data.match(CSS_REGEX);
            if (cssTags && cssTags.length > 0) {
                cssFiles = cssTags.map(tag => {
                    const cssPath = tag.match(/.*href=\"(.*)\".*/i)[1];
                    return url.parse(cssPath).pathname;
                });
                for (const cssFile of cssFiles) {
                    originalCssSize = originalCssSize + fs.statSync(cssFile).size;
                    // Create a copy so we have the original
                    const oldFilePath = path.parse(cssFile);
                    oldFilePath.base = oldFilePath.name + ".original" + oldFilePath.ext;
                    const newPath = path.format(oldFilePath);
                    fs.createReadStream(cssFile).pipe(fs.createWriteStream(newPath));
                }
            }

            critical.generate({
                inline: false,
                base: '.',
                html: data,
                dest: 'critical-only.css',
                minify: true,
                width: 1024,
                height: 800
            }, (err, output) => {
                if (err) {
                    console.log(err);
                    done(err);
                    return;
                }

                // Write the output to the original css so we can verify the result
                fs.writeFileSync(cssFiles[0], output);

                // Verified result, css load time takes 230ms after processing with critical before 4.22s (on 3G good)
                // Some issues found:
                // 1. Hard to do for secured webpages as we are using a proxy to a php backend (altough if we load the main css in the background on the login page this should be more or less ok as on the second visit the bundle is there)
                // 2. Inline option for cricitical css is preferred as it eliminates another http request, this is hard as we need to inject this into the template (twig) somehow
                
                console.log(`Reduced css size for url ${urlToCheck} from ${originalCssSize} to ${output.length}`);
                done();
            });
        });
    });

}

adminBundle.tasks.splitCriticalCss = function splitCriticalCss(done) {
    const urlsToCheck = [
        `${consoleArguments.backendProxy}en/admin/login`,
        //`${consoleArguments.backendProxy}en`,
    ];

    let urlsProcessed = 0;
    for (const urlToCheck of urlsToCheck) {
        extractCriticalCss(urlToCheck, () => {
            urlsProcessed++;
            if (urlsProcessed === urlsToCheck.length) {
                done();
            }
        });
    }
};