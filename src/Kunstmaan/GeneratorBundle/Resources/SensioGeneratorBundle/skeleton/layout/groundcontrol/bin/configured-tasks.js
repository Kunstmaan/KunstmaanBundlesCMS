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
    src: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/img/**',
    dest: './web/frontend/img'
});

export const eslint = createEslintTask({
    src: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/js/**/*.js',
    failAfterError: !consoleArguments.continueAfterTestError,
});

export const stylelint = createStylelintTask({
    src: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/**/*.scss',
});

export const clean = createCleanTask({
    target: ['./web/frontend'],
});

export const copy = gulp.parallel(
{% if demosite %}
    createCopyTask({src: ['./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/files/**'], dest: './web/frontend/files'}),
{% endif %}
    createCopyTask({src: ['./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/fonts/**'], dest: './web/frontend/fonts'})
);

export const cssLocal = createCssLocalTask({
    src: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/style.scss',
    dest: './web/frontend/css',
});

export const cssOptimized = createCssOptimizedTask({
    src: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/*.scss',
    dest: './web/frontend/css',
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
            'web/frontend/css/*.css',
            'web/frontend/js/*.js',
            'web/frontend/img/**/*',
            'web/frontend/styleguide/*.html',
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
    src: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/**/*.scss',
    dest: './web/frontend/styleguide',
    template: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/styleguide/templates/layout.hbs',
    partials: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/styleguide/templates/partials/*.hbs',
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
    dest: './web/frontend/styleguide/css',
});

export const cssStyleguideOptimized = createCssOptimizedTask({
    src: './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/styleguide/scss/*.scss',
    dest: './web/frontend/styleguide/css',
});

export const bundleStyleguideOptimized = createBundleTask({
    config: webpackConfigStyleguide(consoleArguments.speedupLocalDevelopment, true),
});

export function buildOnChange(done) {
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/js/**/!(*.spec).js', bundleLocal);
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/admin/js/**/!(*.spec).js', bundleAdminExtraLocal);
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/**/*.scss', cssLocal);
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/img/**', images);
    done();
}

export function testOnChange(done) {
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/js/**/*.js', eslint);
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/**/*.scss', stylelint);
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/**/*.scss', cssLocal);
    gulp.watch([
        './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/**/*.md',
        './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/styleguide/**/*.hbs',
    ], generateStyleguide);
    gulp.watch('./src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/styleguide/scss/**/*.scss', cssStyleguideOptimized);
    done();
}
