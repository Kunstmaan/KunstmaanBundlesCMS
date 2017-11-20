import defaultConfig from './webpack.config.default';
import webpack from 'webpack';

export default function config(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/js/app.js';
    config.output = {
        filename: './web/frontend/js/bundle.js'
    };
{% if demosite %}
    config.module.rules = config.module.rules.concat([
        {
            test: /\/cargobay\/.+\.scroll-to-top\.js/,
            use: 'exports-loader?cargobay.scrollToTop'
        },
        {
            test: /\/cargobay\/.+\.sidebar-toggle\.js/,
            use: 'exports-loader?cargobay.sidebarToggle'
        },
        {
            test: /\/cargobay\/.+\.toggle\.js/,
            use: 'exports-loader?cargobay.toggle'
        }
    ]);
    config.plugins.push(
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery'
        }));
{% endif %}

    return config;
};