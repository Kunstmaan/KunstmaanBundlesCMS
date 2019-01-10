import defaultConfig from './webpack.config.default';

export default function webpackConfigAdminExtra(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './assets/admin/js/admin-bundle-extra.js';
    config.output = {
        filename: './public/frontend/js/admin-bundle-extra.js',
    };

    return config;
}
