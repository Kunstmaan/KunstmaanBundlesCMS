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
import createStyleguideTask from './tasks/livingcss';
import webpackConfigApp from './config/webpack.config.app';
import webpackConfigAdminExtra from './config/webpack.config.admin-extra';
import webpackConfigStyleguide from './config/webpack.config.styleguide';

export const images = createImagesTask({
    src: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/img/**',
    dest: './{% if isV4 %}public{% else %}web{% endif %}/frontend/img'
});

export const eslint = createEslintTask({
    src: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/js/**/*.js',
    failAfterError: !consoleArguments.continueAfterTestError,
});

export const stylelint = createStylelintTask({
    src: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/scss/**/*.scss',
});

export const clean = createCleanTask({
    target: ['./{% if isV4 %}public{% else %}web{% endif %}/frontend'],
});

export const copy = gulp.parallel(
{% if demosite %}
    createCopyTask({src: ['./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/files/**'], dest: './{% if isV4 %}public{% else %}web{% endif %}/frontend/files'}),
{% endif %}
    createCopyTask({src: ['./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/fonts/**'], dest: './{% if isV4 %}public{% else %}web{% endif %}/frontend/fonts'})
);

export const cssLocal = createCssLocalTask({
    src: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/scss/style.scss',
    dest: './{% if isV4 %}public{% else %}web{% endif %}/frontend/css',
});

export const cssOptimized = createCssOptimizedTask({
    src: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/scss/*.scss',
    dest: './{% if isV4 %}public{% else %}web{% endif %}/frontend/css',
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
            '{% if isV4 %}public{% else %}web{% endif %}/frontend/css/*.css',
            '{% if isV4 %}public{% else %}web{% endif %}/frontend/js/*.js',
            '{% if isV4 %}public{% else %}web{% endif %}/frontend/img/**/*',
            '{% if isV4 %}public{% else %}web{% endif %}/frontend/styleguide/*.html',
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

export const generateStyleguide = createStyleguideTask({
    src: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/scss/**/*.scss',
    dest: './{% if isV4 %}public{% else %}web{% endif %}/frontend/styleguide',
    template: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/styleguide/templates/layout.hbs',
    partials: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/styleguide/templates/partials/*.hbs',
    sortOrder: [
        {
            Index: [
                'Colors',
                'Typography',
                'Blocks',
                'Pageparts',
            ],
        },
    ],
});

export const copyStyleguide = createCopyTask({
    src: ['./node_modules/prismjs/themes/prism-okaidia.css'],
    dest: './{% if isV4 %}public{% else %}web{% endif %}/frontend/styleguide/css',
});

export const cssStyleguideOptimized = createCssOptimizedTask({
    src: './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/styleguide/scss/*.scss',
    dest: './{% if isV4 %}public{% else %}web{% endif %}/frontend/styleguide/css',
});

export const bundleStyleguideOptimized = createBundleTask({
    config: webpackConfigStyleguide(consoleArguments.speedupLocalDevelopment, true),
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
    gulp.watch([
        './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/scss/**/*.md',
        './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/styleguide/**/*.hbs',
    ], generateStyleguide);
    gulp.watch('./{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/styleguide/scss/**/*.scss', cssStyleguideOptimized);
    done();
}
