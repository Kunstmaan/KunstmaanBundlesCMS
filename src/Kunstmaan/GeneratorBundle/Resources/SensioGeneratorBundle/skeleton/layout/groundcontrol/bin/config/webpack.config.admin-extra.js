import defaultConfig from './webpack.config.default';

export default function config(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/admin/js/admin-bundle-extra.js';
    config.output = {
        filename: './web/frontend/js/admin-bundle-extra.js'
    };

    return config;
};