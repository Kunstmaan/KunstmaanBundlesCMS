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

export default function createBuildGroundControlSkeletonTask(skeletonPath, namespace = 'kuma/my-project') {
    const distPath = skeletonPath + '/dist';
    const appPath = distPath + '/src/' + namespace;
    const jsPath = appPath + '/Resources/ui/js';
    const scssPath = appPath + '/Resources/ui/scss';
    const adminJsPath = appPath + '/Resources/admin/js';

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
                    demosite: false
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
            // Style guide
            fs.copySync(skeletonPath + '/../Resources/ui/styleguide', appPath + '/Resources/ui/styleguide');
            // Index.html
            const html = `
                <!DOCTYPE html>
                <html>

                <head>
                    <title>Test page</title>

                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" href="web/frontend/css/style.css"/>
                </head>

                <body>
                    <h1>Test page</h1>
                    <script src="web/frontend/js/bundle.js"></script>
                    <script src="web/frontend/js/admin-bundle-extra.js"></script>
                </body>

                </html>`;

            fs.writeFile(distPath + '/index.html', html, cb);
        },
        function installGroundControlSkeletonNpmPackages(cb) {
            runChildProcess('npm', ['install'], distPath, cb);
        },
        function builGroundControlSkeletonExample(cb) {
            runChildProcess('npm', ['run', 'build'], distPath, cb);
        }
    ];
};
