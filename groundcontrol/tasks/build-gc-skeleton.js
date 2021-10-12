import gulp from 'gulp';
import del from 'del';
import fs from 'fs-extra';
import { spawn } from 'child_process';
import twig from '../gulp-plugins/twig';

const runChildProcess = (executable, args, cwd, cb) => {
    const ls = spawn(executable, args, { cwd });

    ls.stdout.on('data', (data) => {
        console.log(`${data}`);
    });

    ls.stderr.on('data', (data) => {
        console.log(`err: ${data}`);
    });

    ls.on('close', (code) => {
        cb(code !== 0 ? new Error('Execution failed.') : undefined);
    });
};

export default function createBuildGroundControlSkeletonTask(skeletonPath, namespace = 'kuma-my-project') {
    const distPath = skeletonPath + '/dist';
    const appPath = distPath + '/assets';
    const jsPath = appPath + '/ui/js';
    const scssPath = appPath + '/ui/scss';
    const adminJsPath = appPath + '/admin';

    return [
        function cleanGroundControlSkeleton() {
            return del([
                distPath + '/**/*',
                '!' + distPath + '/node_modules',
                '!' + distPath + '/node_modules/**'
            ]);
        },
        function renderGroundControlSkeleton() {
            return gulp.src([skeletonPath + '/**/*', `!${distPath}`, `!${distPath}/**/*`], { dot: true })
                .pipe(twig({
                    bundle: {
                        getName: () => 'test-bundle',
                        namespace
                    },
                    demosite: false,
                    isV4: true
                }))
                .pipe(gulp.dest(distPath));
        },
        function renameGroundControlSkeletonBinDir(cb) {
            fs.move(distPath + '/bin', distPath + '/groundcontrol', cb);
        },
        function addGroundControlSkeletonExampleFiles(cb) {
            fs.ensureDirSync(jsPath);
            fs.writeFileSync(jsPath + '/app.js', 'console.log(\'Hello world\');\n');
            fs.ensureDirSync(adminJsPath);
            fs.writeFileSync(adminJsPath + '/admin-bundle-extra.js', 'console.log(\'Hello world from admin\');\n');
            fs.ensureDirSync(scssPath);
            fs.writeFileSync(scssPath + '/style.scss', 'body { font-size: 20px; }\n');
            // Index.html
            const html = `
                <!DOCTYPE html>
                <html>

                <head>
                    <title>Test page</title>

                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" href="public/build/css/style.css"/>
                </head>

                <body>
                    <h1>Test page</h1>
                    <script src="public/build/js/bundle.js"></script>
                    <script src="public/build/js/admin-bundle-extra.js"></script>
                </body>

                </html>`;

            fs.writeFile(distPath + '/index.html', html, cb);
        },
        function installGroundControlSkeletonNpmPackages(cb) {
            runChildProcess('npm', ['install'], distPath, cb);
        },
        function buildGroundControlSkeletonExample(cb) {
            runChildProcess('npm', ['run', 'build'], distPath, cb);
        }
    ];
};
