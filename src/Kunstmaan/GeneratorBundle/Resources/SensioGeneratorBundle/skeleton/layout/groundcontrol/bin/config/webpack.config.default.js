import TerserPlugin from 'terser-webpack-plugin';
import path from 'path';

function getBabelLoaderOptions({ optimize = false, transpileOnlyForLastChromes = false }) {
    if (optimize || !transpileOnlyForLastChromes) {
        return {
            babelrc: false,
            presets: [
                ['@babel/preset-env', {
                    useBuiltIns: 'usage',
                    modules: false,
                }],
            ],
        };
    }

    return {
        babelrc: false,
        presets: [
            ['@babel/preset-env', {
                useBuiltIns: 'usage',
                targets: {
                    browsers: ['last 2 Chrome versions'],
                },
            }],
        ],
        cacheDirectory: true,
    };
}

function shouldOptimize({ optimize = false }) {
    if (optimize) {
        return {
            minimizer: [new TerserPlugin({
                terserOptions: {
                    mangle: true,
                    sourceMap: true,
                },
            })],
        };
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
                    /**
                     * Exclude all node modules except ES6 packages
                     * This speeds up the build significantly
                     */
                    exclude: [/node_modules\/(?!(quill)\/).*/],
                    use: {
                        loader: 'babel-loader',
                        options: getBabelLoaderOptions({
                            transpileOnlyForLastChromes: speedupLocalDevelopment,
                        }),
                    },
                }, {
                    test: /\.ts$/,
                    use: [{
                        loader: 'ts-loader',
                        options: {
                            compilerOptions: {
                                declaration: false,
                                target: 'es5',
                                module: 'commonjs',
                            },
                            transpileOnly: true,
                        },
                    }],
                }, {
                    test: /\.svg$/,
                    use: [{
                        loader: 'html-loader',
                        options: {
                            minimize: true,
                        },
                    }],
                },
            ],
        },
        optimization: shouldOptimize({ optimize }),
        plugins: [],
    };

    return config;
}
