{% if demosite %}import webpack from 'webpack';
{% endif %}
import path from 'path';
import defaultConfig from './webpack.config.default';

export default function webpackConfigApp(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/js/app.js';
    config.output = {
        path: path.resolve(__dirname, '../../web/frontend/js'),
        filename: 'bundle.js',
    };
{% if demosite %}
    config.module.rules = config.module.rules.concat([
        {
            test: /\/cargobay\/.+\.scroll-to-top\.js/,
            use: 'exports-loader?cargobay.scrollToTop',
        },
        {
            test: /\/cargobay\/.+\.sidebar-toggle\.js/,
            use: 'exports-loader?cargobay.sidebarToggle',
        },
        {
            test: /\/cargobay\/.+\.toggle\.js/,
            use: 'exports-loader?cargobay.toggle',
        },
    ]);
    config.plugins.push(new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    }));
{% endif %}

    return config;
}
