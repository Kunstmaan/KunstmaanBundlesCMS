import path from 'path';
import defaultConfig from './webpack.config.default';

export default function webpackConfigStyleguide(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './{% if isV4 %}assets{% else %}src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources{% endif %}/ui/styleguide/js/styleguide.js';
    config.output = {
        path: path.resolve(__dirname, '../../{% if isV4 %}public{% else %}web{% endif %}/frontend/styleguide/js'),
        filename: 'styleguide.js',
    };

    return config;
}
