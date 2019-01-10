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
    src: './assets/ui/img/**',
    dest: './public/frontend/img'
});

export const eslint = createEslintTask({
    src: './assets/ui/js/**/*.js',
    failAfterError: !consoleArguments.continueAfterTestError,
});

export const stylelint = createStylelintTask({
    src: './assets/ui/scss/**/*.scss',
});

export const clean = createCleanTask({
    target: ['./public/frontend'],
});

export const copy = gulp.parallel(
    createCopyTask({src: ['./assets/ui/fonts/**'], dest: './public/frontend/fonts'})
);

export const cssLocal = createCssLocalTask({
    src: './assets/ui/scss/style.scss',
    dest: './public/frontend/css',
});

export const cssOptimized = createCssOptimizedTask({
    src: './assets/ui/scss/*.scss',
    dest: './public/frontend/css',
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
            'public/frontend/css/*.css',
            'public/frontend/js/*.js',
            'public/frontend/img/**/*',
            'public/frontend/styleguide/*.html',
        ],
        open: false,
        reloadOnRestart: true,
<?php if ($browserSyncUrl) { ?>
        proxy: {
            target: '<?=$browserSyncUrl; ?>',
        },
<?php } else { ?>
        server: {
            baseDir: '.',
        },
<?php } ?>
        notify: true,
    },
});

export const generateStyleguide = createStyleguideTask({
    src: './assets/ui/scss/**/*.scss',
    dest: './public/frontend/styleguide',
    template: './assets/ui/styleguide/templates/layout.hbs',
    partials: './assets/ui/styleguide/templates/partials/*.hbs',
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
    dest: './public/frontend/styleguide/css',
});

export const cssStyleguideOptimized = createCssOptimizedTask({
    src: './assets/ui/styleguide/scss/*.scss',
    dest: './public/frontend/styleguide/css',
});

export const bundleStyleguideOptimized = createBundleTask({
    config: webpackConfigStyleguide(consoleArguments.speedupLocalDevelopment, true),
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
    gulp.watch([
        './assets/ui/scss/**/*.md',
        './assets/ui/styleguide/**/*.hbs',
    ], generateStyleguide);
    gulp.watch('./assets/ui/styleguide/scss/**/*.scss', cssStyleguideOptimized);
    done();
}
