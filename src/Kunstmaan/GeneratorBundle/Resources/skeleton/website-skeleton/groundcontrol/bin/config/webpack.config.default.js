import TerserPlugin from 'terser-webpack-plugin';

function getBabelLoaderOptions({ optimize = false, transpileOnlyForLastChromes = false }) {
    if (optimize || !transpileOnlyForLastChromes) {
        return {
            babelrc: false,
            presets: [
                ['@babel/preset-env', {
                    useBuiltIns: 'usage',
                    modules: false
                }]
            ],
        };
    }

    return {
        babelrc: false,
        presets: [
            ['@babel/preset-env', {
                useBuiltIns: 'usage',
                targets: {
                    browsers: ['last 2 Chrome versions']
                }
            }]
        ],
        cacheDirectory: true,
    };
}

function shouldOptimize({optimize = false}) {
    if (optimize) {
        return {
            minimizer: [new TerserPlugin({
                terserOptions: {
                    mangle: true,
                    sourceMap: true
                }
            })],
        }
    }
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
        optimization: shouldOptimize({optimize: optimize}),
        plugins: [],
    };

    return config;
}
