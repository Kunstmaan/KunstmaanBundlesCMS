import path from 'path';
import defaultConfig from './webpack.config.default';

export default function webpackConfigStyleguide(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/styleguide/js/styleguide.js';
    config.output = {
        path: path.resolve(__dirname, '../../web/frontend/styleguide/js'),
        filename: 'styleguide.js',
    };

    return config;
}
