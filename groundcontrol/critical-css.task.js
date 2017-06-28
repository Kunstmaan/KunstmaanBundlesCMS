import { adminBundle } from './admin-bundle.tasks';
import http from 'http';
import critical from 'critical';
import fs from 'fs';
import url from 'url';
import path from 'path';
import consoleArguments from './console-arguments';
import chalk from 'chalk';

// TODO 
// 1. Remove example of custom home page, focus on the kunstmaan bundles
// 2. Auto inject critical css into the template (src and proxy application), delay loading of main css (in background)
// 3. Add more urls
// 4. Find a way to do this on "secured" pages

// Some issues found:
// 1. Hard to do for secured webpages as we are using a proxy to a php backend (altough if we load the main css in the background on the login page this should be more or less ok as on the second visit the bundle is there)
// 2. Inline option for cricitical css is preferred as it eliminates another http request, this is hard as we need to inject this into the template (twig) and it kinda pollutes the template somewhat
// 3. Loads css files from the file system, doesn't seem to work for dynamic css files
// 4. We cannot rely on the proxy at build time, should create static html for the build to run on which requires manual updates by the developer

// Verified result on 3G good
const PAGES_TO_OPTIMIZE = [
    // css load time takes 230ms after processing with critical before 4.22s (size down to 5KB, from 200kb)
    {
        url: `${consoleArguments.backendProxy}en/admin/login`,
        targetFileName: 'login.html',
        basePath: '.',
        distPath: adminBundle.config.distPath.substring(1)
    },
    // css load time takes 230ms after processing with critical before 550ms (size down to 3,5KB, from 19kb)
    {
        url: `${consoleArguments.backendProxy}en`,
        targetFileName: 'home.html',
        basePath: '../../',
        distPath: 'frontendpoc/data/frontendpoc/web/frontend/'
    }
];
const CSS_REGEX = /(<link\s+rel=\"stylesheet\"\s+href=\").*(css\/.*\.css.*\"\s?(>|\/>))/gmi;

const getTargetPath = item => {
    const { url: itemUrl, distPath, basePath } = item;
    return basePath + distPath + 'generated-static-html/' + item.targetFileName;
};

// Create target dir if it doesn't exist
const createTargetDir = (targetDir, done) => {
    fs.stat(targetDir, (err, stats) => {
        if (err || !stats.isDirectory()) {
            fs.mkdir(targetDir, done);
            return;
        }
        done();
    });
};

const writeStaticHtml = (item, done) => {
    const { url: itemUrl, distPath } = item;
    http.get(itemUrl, res => {
        res.on('data', chunk => {
            const originalData = chunk.toString();
            const data = originalData.replace(CSS_REGEX, `$1${distPath}$2`);
            const targetPath = getTargetPath(item);
            const targetDir = path.dirname(targetPath);
            createTargetDir(targetDir, err => {
                if (err) {
                    done(err);
                    return;
                }
                fs.writeFile(getTargetPath(item), data, done);
            });
        });
    });
};

const extractCriticalCss = (item, done) => {
    const basePath = item.basePath;
    const distPath = item.distPath;
    const urlToCheck = item.url;

    const staticHtmlPath = getTargetPath(item);
    fs.readFile(staticHtmlPath, (err, data) => {
        if (err) {
            done(err);
            return;
        }

        let cssFiles = [];
        let originalCssSize = 0;
        // Rewrite the url to the css bundles dist path
        const cssTags = data.toString().match(CSS_REGEX);
        if (cssTags && cssTags.length > 0) {
            cssFiles = cssTags.map(tag => {
                const cssPath = tag.match(/.*href=\"(.*)\".*/i)[1];
                return url.parse(cssPath).pathname;
            });
            for (const cssFile of cssFiles) {
                const fullPath = basePath + cssFile;
                originalCssSize = originalCssSize + fs.statSync(fullPath).size;
            }
        }

        critical.generate({
            inline: false,
            base: basePath,
            dest: 'styles-critical.css',
            html: data,
            minify: true,
            width: 1024,
            height: 800
        }, (err, output) => {
            if (err) {
                console.log(err);
                done(err);
                return;
            }

            console.log(`Reduced css size for url ${urlToCheck} from ${originalCssSize} to ${output.length}`);
            done();
        });
    });
};

export function generateStaticHtml(done) {
    let itemsProcessed = 0;
    let hasError = false;
    for (const itemToCheck of PAGES_TO_OPTIMIZE) {
        console.log(chalk.blue('Generating static html for '), chalk.yellow(itemToCheck.url));
        writeStaticHtml(itemToCheck, err => {
            itemsProcessed++;
            if (err) {
                console.log(chalk.red(err.message));
                hasError = true;
            }
            if (itemsProcessed === PAGES_TO_OPTIMIZE.length) {
                done(hasError ? new Error('Generating static html failed') : undefined);
            }
        });
    }
};

export function splitCriticalCss(done) {
    let itemsProcessed = 0;
    let hasError = false;
    for (const itemToCheck of PAGES_TO_OPTIMIZE) {
        console.log(chalk.blue('Generating critical css for '), chalk.yellow(itemToCheck.url));
        extractCriticalCss(itemToCheck, err => {
            itemsProcessed++;
            if (err) {
                console.log(chalk.red(err.message));
                hasError = true;
            }
            if (itemsProcessed === PAGES_TO_OPTIMIZE.length) {
                done(hasError ? new Error('Generating critical css failed') : undefined);
            }
        });
    }
};