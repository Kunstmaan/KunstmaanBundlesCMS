import path from 'path';
import defaultConfig from './webpack.config.default';

export default function webpackConfigStyleguide(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './assets/ui/styleguide/js/styleguide.js';
    config.output = {
        path: path.resolve(__dirname, '../../public/frontend/styleguide/js'),
        filename: 'styleguide.js',
    };

    return config;
}
