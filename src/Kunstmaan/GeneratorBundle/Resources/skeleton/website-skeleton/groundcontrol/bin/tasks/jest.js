import jestCli from 'jest-cli';

export default function createJestTask({
    config = undefined,
    root = '.',
    failAfterError = true,
}) {
    return function jest(done) {
        const onComplete = (runResults) => {
            if (runResults.success === false && failAfterError === true) {
                done(new Error('[jest] at least 1 test failed'));
            } else {
                done();
            }
        };

        jestCli.runCLI({ config }, root, onComplete);
    };
}
