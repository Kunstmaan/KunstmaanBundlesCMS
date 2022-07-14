import defaultConfig from './webpack.config.default';
import path from 'path';

export default function webpackConfigAdminExtra(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './assets/admin/js/admin-bundle-extra.js';
    config.output = {
        path: path.resolve(__dirname, '../../public/frontend/js'),
        filename: 'admin-bundle-extra.js',
    };

    return config;
}
