/* eslint-env node */

import gulp from 'gulp';
import webpack from 'webpack';

import consoleArguments from './console-arguments';

import createEslintTask from './tasks/eslint';
import createStylelintTask from './tasks/stylelint';
import createCleanTask from './tasks/clean';
import createCopyTask from './tasks/copy';
import {createCssLocalTask, createCssOptimizedTask} from './tasks/css';
import createBundleTask from './tasks/bundle';
import createServerTask from './tasks/server';
import createHologramTask from './tasks/hologram';

export const eslint = createEslintTask({
    src: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/js/**/*.js',
    failAfterError: !consoleArguments.continueAfterTestError
});

export const stylelint = createStylelintTask({src: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/**/*.scss'});

export const clean = createCleanTask({target: ['./web/frontend']});

export const copy = gulp.parallel(
    createCopyTask({src: ['./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/img/**'], dest: './web/frontend/img'}),
{% if demosite %}
    createCopyTask({src: ['./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/files/**'], dest: './web/frontend/files'}),
{% endif %}
    createCopyTask({src: ['./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/fonts/**'], dest: './web/frontend/fonts'})
);

export const cssLocal = createCssLocalTask({src: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/style.scss', dest: './web/frontend/css'});

export const cssOptimized = createCssOptimizedTask({src: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/*.scss', dest: './web/frontend/css'});

export const bundleLocal = createBundleTask({
    config: {
        entry: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/js/app.js',
        output: {
            filename: './web/frontend/js/bundle.js'
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
                }{% if demosite %},
                {
                    test: /\/cargobay\/.+\.scroll-to-top\.js/,
                    use: 'exports-loader?cargobay.scrollToTop'
                },
                {
                    test: /\/cargobay\/.+\.sidebar-toggle\.js/,
                    use: 'exports-loader?cargobay.sidebarToggle'
                },
                {
                    test: /\/cargobay\/.+\.toggle\.js/,
                    use: 'exports-loader?cargobay.toggle'
                }{% endif %}

            ]
        }{% if demosite %},
        plugins: [
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery',
                'window.jQuery': 'jquery'
            })
        ]{% endif %}

    }
});

export const bundleOptimized = createBundleTask({
    config: {
        entry: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/js/app.js',
        output: {
            filename: './web/frontend/js/bundle.js'
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
                }{% if demosite %},
                {
                    test: /\/cargobay\/.+\.scroll-to-top\.js/,
                    use: 'exports-loader?cargobay.scrollToTop'
                },
                {
                    test: /\/cargobay\/.+\.sidebar-toggle\.js/,
                    use: 'exports-loader?cargobay.sidebarToggle'
                },
                {
                    test: /\/cargobay\/.+\.toggle\.js/,
                    use: 'exports-loader?cargobay.toggle'
                }{% endif %}

            ]
        },
        plugins: [
            new webpack.optimize.UglifyJsPlugin({mangle: true, sourceMap: true}){% if demosite %},
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery',
                'window.jQuery': 'jquery'
            }){% endif %}

        ]
    },
    logStats: true
});

export const bundleAdminExtraLocal = createBundleTask({
    config: {
        entry: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/admin/js/admin-bundle-extra.js',
        output: {
            filename: './web/frontend/js/admin-bundle-extra.js'
        },
        devtool: 'source-map',
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

export const bundleAdminExtraOptimized = createBundleTask({
    config: {
        entry: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/admin/js/admin-bundle-extra.js',
        output: {
            filename: './web/frontend/js/admin-bundle-extra.js'
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
            new webpack.optimize.UglifyJsPlugin({mangle: true, sourceMap: true})
        ]
    }
});

export const server = createServerTask({
    config: {
        ui: false,
        ghostMode: false,
        files: [
            'web/frontend/css/*.css',
            'web/frontend/js/*.js',
            'web/frontend/img/**/*',
            'web/frontend/styleguide/*.html'
        ],
        open: false,
        reloadOnRestart: true,
{% if browserSyncUrl %}
        proxy: {
            target: '{{ browserSyncUrl }}'
        },
{% else %}
        server: {
            baseDir: '.'
        },
{% endif %}
        notify: true
    }
});

export const hologram = createHologramTask({cwd: 'src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/styleguide'});

export function buildOnChange(done) {
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/js/**/!(*.spec).js', bundleLocal);
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/admin/js/**/!(*.spec).js', bundleAdminExtraLocal);
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/**/*.scss', cssLocal);
    done();
}

export function testOnChange(done) {
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/js/**/*.js', eslint);
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/**/*.scss', stylelint);
    done();
}

function getBabelLoaderOptions({optimize = false, transpileOnlyForLastChromes = false}) {
    if (optimize || !transpileOnlyForLastChromes) {
        return {
            babelrc: false,
            presets: [
                ['es2015', {
                    // TODO
                    modules: false
                }]
            ]
        };
    }

    return {
        babelrc: false,
        presets: [
            ['env', {
                targets: {
                    browsers: ['last 2 Chrome versions']
                }
            }]
        ],
        cacheDirectory: true
    };
}
