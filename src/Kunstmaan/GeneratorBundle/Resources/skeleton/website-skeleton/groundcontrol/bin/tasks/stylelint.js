import gulp from 'gulp';
import stylelintPlugin from 'stylelint';
import reporter from 'postcss-reporter';
import postcss from 'gulp-postcss';
import scssSyntax from 'postcss-scss';

export default function createStylelintTask({
    src = undefined,
}) {
    return function stylelint() {
        return gulp.src(src)
            .pipe(postcss([
                stylelintPlugin(),
                reporter({
                    clearReportedMessages: true,
                }),
            ], {
                syntax: scssSyntax,
            }));
    };
}
