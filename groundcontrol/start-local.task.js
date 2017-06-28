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

const BUNDLES = [
    { bundleInfo: adminBundle, url: '/bundles/kunstmaanadmin', },
    { bundleInfo: dashboardBundle, url: '/bundles/kunstmaandashboard' },
    { bundleInfo: mediaBundle, url: '/bundles/kunstmaanmedia' },
    { bundleInfo: translatorBundle, url: '/bundles/kunstmaantranslator' }
];

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
    if (writeToResponse(req, res, BUNDLES.map(item => item.bundleInfo.config.distPath))) {
        // Nothing we can write to the stream, fallback to the default behavior
        console.log(err);
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
        middleware: BUNDLES.map(bundle => { return { route: bundle.url, handle: handleRequest } })
    }
});

export const buildOnChange = (done) => {
    for (const bundle of BUNDLES) {
        const srcPath = bundle.bundleInfo.config.srcPath;
        const jsAssets = srcPath + 'js/**/!(*.spec).js';
        gulp.watch(jsAssets, bundle.bundleInfo.tasks.scripts);
        const styleAssets = srcPath + 'scss/**/*.scss';
        gulp.watch(styleAssets, bundle.bundleInfo.tasks.cssOptimized);
    }
    done();
};

export default startLocalTask;