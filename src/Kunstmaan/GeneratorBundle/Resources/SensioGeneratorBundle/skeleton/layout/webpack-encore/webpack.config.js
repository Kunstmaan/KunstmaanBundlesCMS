const path = require('path');

const Encore = require('@symfony/webpack-encore');
const StylelintPlugin = require('stylelint-webpack-plugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .addAliases({
        scssRootDir: path.resolve(__dirname, 'assets/ui/scss'),
    })
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    // .setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/ui/app.js')
    .addEntry('admin', './assets/admin/admin-bundle-extra.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

{% if demosite %}
    // jQuery plugins often expect that jQuery is already available via the $ or jQuery global variables.
    // To fix this, call autoProvidejQuery() from your webpack.config.js file:
    .autoProvidejQuery()
    // you can use this method to provide other common global variables,
    // such as '_' for the 'underscore' library
    .autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    })

    .addLoader({
        test: /\/cargobay\/.+\.scroll-to-top\.js/,
        use: 'exports-loader?cargobay.scrollToTop',
    })
    .addLoader({
        test: /\/cargobay\/.+\.sidebar-toggle\.js/,
        use: 'exports-loader?cargobay.sidebarToggle',
    })
    .addLoader({
        test: /\/cargobay\/.+\.toggle\.js/,
        use: 'exports-loader?cargobay.toggle',
    })

{% endif %}
    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .enableEslintPlugin()
    .addPlugin(new StylelintPlugin({
        files: ['./assets/**/*.scss'],
    }))

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        // eslint-disable-next-line no-param-reassign
        config.useBuiltIns = 'usage';
        // eslint-disable-next-line no-param-reassign
        config.corejs = 3;
    })

    .enablePostCssLoader()

    // enables Sass/SCSS support
    .enableSassLoader(() => {}, { resolveUrlLoader: false })

    // Copy static files
    .copyFiles({
        from: './assets/ui/img',
        to: 'img/[path][name].[hash:8].[ext]',
    })

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    // .enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()

    .configureDevServerOptions((options) => {
        options.allowedHosts = 'all';
        // in older Webpack Dev Server versions, use this option instead:
        // options.firewall = false;
    });

module.exports = Encore.getWebpackConfig();
