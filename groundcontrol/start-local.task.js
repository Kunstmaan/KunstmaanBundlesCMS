import fs from 'fs';
import url from 'url';
import path from 'path';
import mime from 'mime-types';
import gulp from 'gulp';
import createServerTask from './tasks/server';
import consoleArguments from './console-arguments';
import { adminBundle } from './admin-bundle.tasks';
import { dashboardBundle } from './dashboard-bundle.tasks';
import { mediaBundle } from './media-bundle.tasks';
import { translatorBundle } from './translator-bundle.tasks';

const BUNDLES = [adminBundle, dashboardBundle, mediaBundle, translatorBundle];

const writeToResponse = (req, res, bundlePaths) => {
    const formattedUrl = url.parse(req.url);
    for (const bundlePath of bundlePaths) {
        const filePath = path.normalize(bundlePath + formattedUrl.pathname);
        try {
            const stat = fs.statSync(filePath);
            if (stat && stat.isFile()) {
                const rstream = fs.createReadStream(filePath);
                const extension = path.extname(filePath);
                const contentType = mime.lookup(extension);
                res.writeHead(200, {
                    'Content-Type': contentType,
                    'Content-Length': stat.size
                });
                rstream.pipe(res);
                return;
            }
        } catch (e) {
            // Does not exist
        }
    }
    return new Error(`Local file for ${req.url} not found`);
};

const handleRequest = (req, res, next) => {
    if (writeToResponse(req, res, BUNDLES.map(item => item.config.distPath))) {
        // Nothing we can write to the stream, fallback to the default behavior
        return next();
    };
};

const startLocalTask = createServerTask({
    config: {
        ui: false,
        ghostMode: false,
        open: false,
        reloadOnRestart: true,
        notify: true,
        proxy: { target: consoleArguments.backendProxy },
        middleware: BUNDLES.map(bundle => { return { route: bundle.config.publicPath, handle: handleRequest } })
    }
});

export const buildOnChange = (done) => {
    for (const bundle of BUNDLES) {
        const srcPath = bundle.config.srcPath;

        const jsAssets = srcPath + 'js/**/!(*.spec).js';
        gulp.watch(jsAssets, bundle.tasks.scripts);

        if (bundle.tasks.bundle) {
            const jsNextAssets = srcPath + 'jsnext/**/!(*.spec).js';
            gulp.watch(jsNextAssets, bundle.tasks.bundle);
        }

        const styleAssets = srcPath + 'scss/**/*.scss';
        gulp.watch(styleAssets, bundle.tasks.cssOptimized);

        if (bundle.tasks.cssNextOptimized) {
            const styleNextAssets = srcPath + 'scssnext/**/*.scss';
            gulp.watch(styleNextAssets, bundle.tasks.cssNextOptimized);
        }
    }
    done();
};

export function testOnChange(done) {
    for (const bundle of BUNDLES) {
        if (bundle.tasks.eslint) {
            const srcPath = bundle.config.srcPath;
            gulp.watch(`${srcPath}jsnext/**/*.js`, bundle.tasks.eslint);
        }
        if (bundle.tasks.stylelint) {
            const srcPath = bundle.config.srcPath;
            gulp.watch(`${srcPath}scssnext/**/*.scss`, bundle.tasks.stylelint);
        }
    }
    done();
}


export default startLocalTask;
