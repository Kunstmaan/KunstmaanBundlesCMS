import webpack from 'webpack';

function getBabelLoaderOptions({optimize = false, transpileOnlyForLastChromes = false}) {
    if (optimize || !transpileOnlyForLastChromes) {
        return {
            babelrc: false,
            presets: [
                require.resolve('babel-preset-env', {
                    modules: false
                })
            ]
        };
    }

    return {
        babelrc: false,
        presets: [
            require.resolve('babel-preset-env', {
                targets: {
                    browsers: ['last 2 Chrome versions']
                },
                debug: true
            })
        ],
        cacheDirectory: true
    };
}

export default function config(speedupLocalDevelopment, optimize = false) {
    const config = {
        devtool: optimize ? 'source-map' : 'cheap-module-source-map',
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loader: 'babel-loader',
                    query: getBabelLoaderOptions({
                        transpileOnlyForLastChromes: speedupLocalDevelopment
                    })
                }
            ]
        },
        plugins: []
    };

    if (optimize) {
        config.plugins.push(new webpack.optimize.UglifyJsPlugin({mangle: true, sourceMap: true}));
    }

    return config;
};
