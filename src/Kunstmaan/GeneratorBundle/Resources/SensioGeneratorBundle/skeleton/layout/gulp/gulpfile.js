'use strict';

/* ==========================================================================
   Gulpfile

   Development-tasks:
   - gulp (build + watch)
   - gulp build
   - gulp build-deploy (does install for bower and npm, then general build)
   - gulp watch

   - gulp migrate
   - gulp cc (Clear Cache)
   - gulp fetch //todo: config in json file
   - gulp fixperms //todo: config in json file
   - gulp maintenance
   - gulp apachectl
   ========================================================================== */


/* Setup Gulp
   ========================================================================== */
// Require Gulp
var gulp = require('gulp');

// Load Gulp plugins
var plugins = require('gulp-load-plugins')();

// Load the notifier.
var Notifier = require('node-notifier');

// Set to false if you don't want notifications when an error happens.
// (Errors will still be logged in Terminal)
var showErrorNotifications = true;

/* Config
   ========================================================================== */
var resourcesPath = './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/';
var distPath = './web/frontend';
var bowerComponentsPath = './app/Resources/vendor_bower';
var {{ bundle.getName() }} = {
    dist: {
        css: distPath + '/css',
        js: distPath + '/js',
        img: distPath + '/img',
        video: distPath + '/video',
        fonts: distPath + '/fonts'
    },

    styleguide: resourcesPath + '/ui/styleguide',
    ScssSourcemapPath: '../../../src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/scss/',

    img: resourcesPath + '/ui/img/**/*.{png,jpg,jpeg,gif,svg,webp}',
    video: resourcesPath + '/ui/video/**/*.{webm,mp4}',
    twig: resourcesPath + '/views/**/*.html.twig',
    scss: resourcesPath + '/ui/scss/**/*.scss',
    js: {
        app: resourcesPath + '/ui/js/**/*.js',
        footer: [
            bowerComponentsPath + '/jquery/dist/jquery.js',
            bowerComponentsPath + '/velocity/velocity.js',
            bowerComponentsPath + '/cargobay/src/scroll-to-top/js/jquery.scroll-to-top.js',
            bowerComponentsPath + '/cargobay/src/toggle/js/jquery.toggle.js',
            resourcesPath + '/ui/js/*.js'
        ],
        other: [
            bowerComponentsPath + '/html5shiv/dist/html5shiv.min.js'
        ]
    },

    liveReloadFiles: [
        distPath + '/css/style.min.css',
        distPath + '/js/footer.min.js'
    ],

    liveReloadFilesStyleguide: [
        distPath + '/styleguide/css/style.min.css'
    ]
};


/* Errorhandling
   ========================================================================== */

var errorLogger = function(headerMessage,errorMessage){
    var header = headerLines(headerMessage);
        header += '\n             '+ headerMessage +'\n           ';
        header += headerLines(headerMessage);
        header += '\r\n \r\n';
    plugins.util.log(plugins.util.colors.red(header) + '             ' + errorMessage + '\r\n')

    if(showErrorNotifications){
        var notifier = new Notifier();
        notifier.notify({
            'title': headerMessage,
            'message': errorMessage,
            'contentImage':  __dirname + "/gulp_error.jpg"
        });
    }
}

var headerLines = function(message){
    var lines = '';
    for(var i = 0; i< (message.length + 4); i++){
        lines += '-';
    }
    return lines;
}

/* Tasks
   ========================================================================== */
// Styles
gulp.task('styles', function() {
    return gulp.src({{ bundle.getName() }}.scss)
        // Scss -> Css
        .pipe(plugins.rubySass({
            sourcemap: false,
            loadPath: './',
            bundleExec: true
        }))
        .on('error', function (err){
            errorLogger('SASS Compilation Error', err.message);
        })

        // Combine Media Queries
        .pipe(plugins.combineMediaQueries())

        // Prefix where needed
        .pipe(plugins.autoprefixer('last 2 versions', 'ie 9', 'ie 10', 'ie 11'))

        // Remove all comments
        .pipe(plugins.stripCssComments())

        // Minify output
        .pipe(plugins.minifyCss())

        // Rename the file to respect naming covention.
        .pipe(plugins.rename(function(path){
            path.basename += '.min';
        }))

        // Write to output
        .pipe(gulp.dest({{ bundle.getName() }}.dist.css))

        // Show total size of css
        .pipe(plugins.size({
            title: 'css'
        }));
});


// Jshint
gulp.task('jshint', function () {
    return gulp.src([{{ bundle.getName() }}.js.app, '!' + resourcesPath + '/ui/js/vendors/**/*.js'])
        // Jshint
        .pipe(plugins.jshint())
        .pipe(plugins.jshint.reporter(require('jshint-stylish')));
});

// Scripts
gulp.task('scripts', ['jshint'], function () {
    var footerjs = gulp.src({{ bundle.getName() }}.js.footer)
        // Uglify
        .pipe(plugins.uglify({
            mangle: {
                except: ['jQuery']
            }
        }))
        .on('error', function (err){
            errorLogger('Javascript Error', err.message);
        })

        // Concat
        .pipe(plugins.concat('footer.min.js'))

        // Set desitination
        .pipe(gulp.dest({{ bundle.getName() }}.dist.js))

        // Show total size of js
        .pipe(plugins.size({
            title: 'js'
        }));

    var otherjs = gulp.src({{ bundle.getName() }}.js.other)
        // Uglify
        .pipe(plugins.uglify({
            mangle: {
                except: ['jQuery']
            }
        }))
        .on('error', function (err){
            errorLogger('Javascript Error', err.message);
        })

        // Set desitination
        .pipe(gulp.dest({{ bundle.getName() }}.dist.js))

        // Show total size of js
        .pipe(plugins.size({
            title: 'js'
        }));

    var merge = require('merge-stream');

    return merge(footerjs, otherjs);
});


// Images
gulp.task('images', function () {
    return gulp.src({{ bundle.getName() }}.img)
        // Only optimize changed images
        .pipe(plugins.changed({{ bundle.getName() }}.dist.img))

        // Imagemin
        .pipe(plugins.imagemin({
            optimizationLevel: 3,
            progressive: true,
            svgoPlugins: [{
                removeViewBox: false
            }]
        }))

        // Set desitination
        .pipe(gulp.dest({{ bundle.getName() }}.dist.img))

        // Show total size of images
        .pipe(plugins.size({
            title: 'images'
        }));
});

// Videos
gulp.task('videos', function () {
    return gulp.src({{ bundle.getName() }}.video)
        // Set desitination
        .pipe(gulp.dest({{ bundle.getName() }}.dist.video))

        // Show total size of images
        .pipe(plugins.size({
            title: 'videos'
        }));
});


// Styleguide -> Change it by https://github.com/rejahrehim/gulp-hologram when it supports bundler
gulp.task('styleguide', function () {
    return gulp.src({{ bundle.getName() }}.styleguide, {read: false})
        .pipe(plugins.shell([
            'bundle exec hologram',
        ], {
            cwd: {{ bundle.getName() }}.styleguide
        }));
});


// Migrate
gulp.task('migrate', plugins.shell.task([
    'app/console doctrine:migrations:migrate --no-interaction'
]));

// Clear Cache
gulp.task('cc', plugins.shell.task([
    'php app/console cache:clear',
    'php app/console assetic:dump',
    'php app/console assets:install web --symlink'
]));

// // Fetch
// gulp.task('fetch', plugins.shell.task([
//     'kms fetch --project projectname --server servername'
// ]));

// // Fix perms
// gulp.task('fixperms', plugins.shell.task([
//     'sudo python fixperms.py projectname'
// ], {
//     cwd: '/opt/kDeploy/tools'
// }));

// Maintenance
gulp.task('maintenance', plugins.shell.task([
    'sudo python maintenance.py quick'
], {
    cwd: '/opt/kDeploy/tools'
}));

// Restart Apache
gulp.task('apachectl', plugins.shell.task([
    'sudo apachectl restart'
], {
    cwd: '/opt/kDeploy/tools'
}));


// Install for Bower & npm
gulp.task('install_npm_bower', plugins.shell.task([
    'npm install',
    'bower install'
]));


// Watch
gulp.task('watch', function () {
    // Livereload
    plugins.livereload.listen();
    gulp.watch({{ bundle.getName() }}.liveReloadFiles).on('change', function(file) {
        plugins.livereload.changed(file.path);
        gulp.start('styleguide');
    });

    gulp.watch({{ bundle.getName() }}.liveReloadFilesStyleguide).on('change', function(file) {
        plugins.livereload.changed(file.path);
    });

    // Styles
    gulp.watch({{ bundle.getName() }}.scss, ['styles']);

    // Scripts
    gulp.watch({{ bundle.getName() }}.js.app, ['scripts']);

    // Images
    gulp.watch({{ bundle.getName() }}.img, ['images']);
});


// Build
gulp.task('build', ['styles', 'scripts', 'images', 'videos'], function() {
    gulp.start('styleguide');
});


//Build Deploy
gulp.task('build-deploy', ['install_npm_bower'], function() {
    gulp.start('build');
});


// Default
gulp.task('default', ['build'], function () {
    gulp.start('watch');
});
