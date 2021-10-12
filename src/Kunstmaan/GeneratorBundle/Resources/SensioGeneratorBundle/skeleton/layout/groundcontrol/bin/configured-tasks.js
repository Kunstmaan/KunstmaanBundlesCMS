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
    src: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/img/**',
    dest: './{% if isV4 %}public{% else %}web{% endif %}/build/img'
});

export const eslint = createEslintTask({
    src: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/js/**/*.js',
    failAfterError: !consoleArguments.continueAfterTestError,
});

export const stylelint = createStylelintTask({
    src: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/scss/**/*.scss',
});

export const clean = createCleanTask({
    target: ['./{% if isV4 %}public{% else %}web{% endif %}/build'],
});

export const copy = gulp.parallel(
{% if demosite %}
    createCopyTask({src: ['./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/files/**'], dest: './{% if isV4 %}public{% else %}web{% endif %}/build/files'}),
{% endif %}
    createCopyTask({src: ['./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/fonts/**'], dest: './{% if isV4 %}public{% else %}web{% endif %}/build/fonts'})
);

export const cssLocal = createCssLocalTask({
    src: ['./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/scss/style.scss', './assets/admin/*.scss'],
    dest: './{% if isV4 %}public{% else %}web{% endif %}/build/css',
});

export const cssOptimized = createCssOptimizedTask({
    src: ['./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/scss/*.scss', './assets/admin/*.scss'],
    dest: './{% if isV4 %}public{% else %}web{% endif %}/build/css',
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
            '{% if isV4 %}public{% else %}web{% endif %}/build/css/*.css',
            '{% if isV4 %}public{% else %}web{% endif %}/build/js/*.js',
            '{% if isV4 %}public{% else %}web{% endif %}/build/img/**/*',
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
    gulp.watch('./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/js/**/!(*.spec).js', bundleLocal);
    gulp.watch('./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/admin/js/**/!(*.spec).js', bundleAdminExtraLocal);
    gulp.watch('./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/scss/**/*.scss', cssLocal);
    gulp.watch('./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/img/**', images);
    done();
}

export function testOnChange(done) {
    gulp.watch('./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/js/**/*.js', eslint);
    gulp.watch('./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/scss/**/*.scss', stylelint);
    gulp.watch('./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/scss/**/*.scss', cssLocal);
    done();
}
