import yargs from 'yargs';
import chalk from 'chalk';

const OPTIONS = {
    logBundleStats: {
        alias: 'logStats',
        describe: 'Should stats from bundling be logged?',
        default: false,
        type: 'boolean',
    },
    continueAfterTestError: {
        describe: 'Should the (watch) process continue after a lint/jest error or not?',
        default: false,
        type: 'boolean',
    },
    speedupLocalDevelopment: {
        alias: 'speedupLocalDev',
        describe: 'Should local dev be speeded up, but at cost of slightly different setup vs. the prod build?',
        default: false,
        type: 'boolean',
    },
};

const { argv } = yargs.options(OPTIONS);

console.log(getCurrentArgumentsInfo(argv, OPTIONS));

module.exports = argv;

function getCurrentArgumentsInfo(currentArgs, options) {
    const initialResultInfo = `\n${chalk.cyan('[Arguments]')}\n`;

    return Object
        .keys(options)
        .reduce(
            (resultInfo, optionKey) => `${resultInfo}- ${getArgumentInfo(optionKey)}`,
            initialResultInfo,
        );

    function getArgumentInfo(optionKey) {
        const option = options[optionKey];
        // eslint-disable-next-line max-len
        return `${chalk.bold.green(optionKey)}${getAliasInfo(option)} | ${option.describe} : ${chalk.bold.red(currentArgs[optionKey])}\n`;
    }

    function getAliasInfo(option) {
        if (option.alias) {
            return ` [${option.alias}]`;
        }

        return '';
    }
}
