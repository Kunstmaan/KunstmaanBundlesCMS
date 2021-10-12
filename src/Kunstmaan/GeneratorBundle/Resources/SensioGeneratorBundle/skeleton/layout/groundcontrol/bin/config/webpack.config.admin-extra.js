import path from 'path';
import defaultConfig from './webpack.config.default';

export default function webpackConfigAdminExtra(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/admin/admin-bundle-extra.js';
    config.output = {
        path: path.resolve(__dirname, '../../{% if isV4 %}public{% else %}web{% endif %}/build/js'),
        filename: 'admin-bundle-extra.js',
    };

    return config;
}
