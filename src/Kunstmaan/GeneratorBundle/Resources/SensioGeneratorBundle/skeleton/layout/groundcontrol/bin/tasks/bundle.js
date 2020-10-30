import webpack from 'webpack';

/**
 * You can let webpack watch the files and rebundle on change, or you can do it
 * via gulp. Gulp will probably be easier at first, since you have to configure
 * multiple watches for the other src files as well.
 */
export default function createBundleTask({config = undefined, logStats = false}) {
    const compiler = webpack(config);
    const devMode = config.mode === 'development';

    return function bundle(done) {
        compiler.run(handleWebpackResult);

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
                if (devMode) {
                    console.error('\x1b[31m%s\x1b[0m: ', info.errors.toString());
                } else {
                    throw Error(info.errors.toString());
                }
            }

            if (stats.hasWarnings()) {
                console.warn(info.warnings.toString());
            }

            if (logStats) {
                console.log(stats.toString());
            }
            done();
        }
    };
}
