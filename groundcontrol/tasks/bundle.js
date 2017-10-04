import webpack from 'webpack';

/**
 * You can let webpack watch the files and rebundle on change, or you can do it
 * via gulp. Gulp will probably be easier at first, since you have to configure
 * multiple watches for the other src files as well.
 */
export default function createBundleTask({config = undefined, watch = false, logStats = false}) {
    const compiler = webpack(config);

    return function bundle(done) {
        if (watch) {
            compiler.watch({}, handleWebpackResult);
        } else {
            compiler.run(handleWebpackResult);
        }
        
        function handleWebpackResult(err, stats) {
            if (err) {
                console.error(err.stack || err);
                if (err.details) {
                    console.error(err.details);
                }
                return;
            }

            const info = stats.toJson();

            if (stats.hasErrors()) {
                console.error(info.errors);
            }

            if (stats.hasWarnings()) {
                console.warn(info.warnings);
            }

            if (logStats) {
                console.log(stats.toString());
            }
            done();
        }
    };
}

export function getBabelLoaderOptions({optimize = false, transpileOnlyForLastChromes = false}) {
    if (optimize || !transpileOnlyForLastChromes) {
        return {
            babelrc: false,
            presets: [
                ['es2015', {
                    // TODO
                    modules: false
                }]
            ]
        };
    }

    return {
        babelrc: false,
        presets: [
            ['env', {
                targets: {
                    browsers: ['last 2 Chrome versions']
                }
            }]
        ],
        cacheDirectory: true
    };
}
