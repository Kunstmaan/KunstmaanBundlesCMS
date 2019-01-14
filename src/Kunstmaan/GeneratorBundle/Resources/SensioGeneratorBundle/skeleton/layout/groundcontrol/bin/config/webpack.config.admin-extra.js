import defaultConfig from './webpack.config.default';

export default function webpackConfigAdminExtra(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/admin/js/admin-bundle-extra.js';
    config.output = {
        filename: './{% if isV4 %}public{% else %}web{% endif %}/frontend/js/admin-bundle-extra.js',
    };

    return config;
}
