function getBabelLoaderOptions({ optimize = false, transpileOnlyForLastChromes = false }) {
    if (optimize || !transpileOnlyForLastChromes) {
        return {
            babelrc: false,
            presets: [
                require.resolve('babel-preset-env', {
                    modules: false,
                }),
            ],
        };
    }

    return {
        babelrc: false,
        presets: [
            require.resolve('babel-preset-env', {
                targets: {
                    browsers: ['>0.25%', 'ie 11', 'not op_mini all'],
                },
                debug: true,
            }),
        ],
        cacheDirectory: true,
    };
}

export default function defaultConfig(speedupLocalDevelopment, optimize = false) {
    const config = {
        mode: optimize ? 'production' : 'development',
        devtool: optimize ? 'source-map' : 'cheap-module-source-map',
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: getBabelLoaderOptions({
                            transpileOnlyForLastChromes: speedupLocalDevelopment,
                        }),
                    },
                },
            ],
        },
        plugins: [],
    };

    return config;
}
