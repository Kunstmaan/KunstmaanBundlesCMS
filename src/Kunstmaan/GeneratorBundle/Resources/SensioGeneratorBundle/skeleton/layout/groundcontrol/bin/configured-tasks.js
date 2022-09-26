import gulp from 'gulp';

import consoleArguments from './console-arguments';

import createImagesTask from './tasks/images';
import createEslintTask from './tasks/eslint';
import createStylelintTask from './tasks/stylelint';
import createCleanTask from './tasks/clean';
import createCopyTask from './tasks/copy';
import { createCssLocalTask, createCssOptimizedTask } from './tasks/css';
import createBundleTask from './tasks/bundle';
import createServerTask from './tasks/server';
import webpackConfigApp from './config/webpack.config.app';
import webpackConfigAdminExtra from './config/webpack.config.admin-extra';

export const images = createImagesTask({
    src: './assets/ui/img/**',
    dest: './public/build/img'
});

export const eslint = createEslintTask({
    src: './assets/ui/js/**/*.js',
    failAfterError: !consoleArguments.continueAfterTestError,
});

export const stylelint = createStylelintTask({
    src: './assets/ui/scss/**/*.scss',
});

export const clean = createCleanTask({
    target: ['./public/build'],
});

export const copy = gulp.parallel(
{% if demosite %}
    createCopyTask({src: ['./assets/ui/files/**'], dest: './public/build/files'}),
{% endif %}
    createCopyTask({src: ['./assets/ui/fonts/**'], dest: './public/build/fonts'})
);

export const cssLocal = createCssLocalTask({
    src: ['./assets/ui/scss/style.scss', './assets/admin/*.scss'],
    dest: './public/build/css',
});

export const cssOptimized = createCssOptimizedTask({
    src: ['./assets/ui/scss/*.scss', './assets/admin/*.scss'],
    dest: './public/build/css',
});

export const bundleLocal = createBundleTask({
    config: webpackConfigApp(consoleArguments.speedupLocalDevelopment),
});

export const bundleOptimized = createBundleTask({
    config: webpackConfigApp(consoleArguments.speedupLocalDevelopment, true),
    logStats: true,
});

export const bundleAdminExtraLocal = createBundleTask({
    config: webpackConfigAdminExtra(consoleArguments.speedupLocalDevelopment),
});

export const bundleAdminExtraOptimized = createBundleTask({
    config: webpackConfigAdminExtra(consoleArguments.speedupLocalDevelopment, true),
});

export const server = createServerTask({
    config: {
        ui: false,
        ghostMode: false,
        files: [
            'public/build/css/*.css',
            'public/build/js/*.js',
            'public/build/img/**/*',
        ],
        open: false,
        reloadOnRestart: true,
{% if browserSyncUrl %}
        proxy: {
            target: '{{ browserSyncUrl }}',
        },
{% else %}
        server: {
            baseDir: '.',
        },
{% endif %}
        notify: true,
    },
});

export function buildOnChange(done) {
    gulp.watch('./assets/ui/js/**/!(*.spec).js', bundleLocal);
    gulp.watch('./assets/admin/js/**/!(*.spec).js', bundleAdminExtraLocal);
    gulp.watch('./assets/ui/scss/**/*.scss', cssLocal);
    gulp.watch('./assets/ui/img/**', images);
    done();
}

export function testOnChange(done) {
    gulp.watch('./assets/ui/js/**/*.js', eslint);
    gulp.watch('./assets/ui/scss/**/*.scss', stylelint);
    gulp.watch('./assets/ui/scss/**/*.scss', cssLocal);
    done();
}
