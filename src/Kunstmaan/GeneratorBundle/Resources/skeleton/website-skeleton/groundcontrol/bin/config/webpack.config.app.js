import path from 'path';
import defaultConfig from './webpack.config.default';

export default function webpackConfigApp(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './assets/ui/js/app.js';
    config.output = {
        path: path.resolve(__dirname, '../../public/frontend/js'),
        filename: 'bundle.js',
    };

    return config;
}
