import path from 'path';
import defaultConfig from './webpack.config.default';

export default function webpackConfigAdminExtra(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './assets/admin/admin-bundle-extra.js';
    config.output = {
        path: path.resolve(__dirname, '../../public/build/js'),
        filename: 'admin-bundle-extra.js',
    };

    return config;
}
