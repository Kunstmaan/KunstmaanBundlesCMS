import defaultConfig from './webpack.config.default';

export default function config(speedupLocalDevelopment, optimize = false) {
    const config = defaultConfig(speedupLocalDevelopment, optimize);

    config.entry = './src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/ui/styleguide/js/styleguide.js';
    config.output = {
        filename: './web/frontend/styleguide/js/styleguide.js'
    };

    return config;
};
