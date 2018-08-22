import gulp from 'gulp';
import livingcss from 'gulp-livingcss';
import path from 'path';

export default function createStyleguideTask({src = undefined, dest = undefined, template = undefined, sortOrder = undefined, partials = undefined}) {
    return function styleguide() {
        return gulp.src(src)
            .pipe(livingcss(dest, {
                template,
                sortOrder,
                preprocess (context, tmpl, Handlebars) {
                    Handlebars.registerHelper('json', function(data) {
                        return JSON.stringify(data);
                    });

                    Handlebars.registerHelper('clean', function(data) {
                        return data.replace(/(\\|%5[cC])/g, '');
                    });

                    return livingcss.utils.readFileGlobs(partials, function(data, file) {

                        // make the name of the partial the name of the file
                        const partialName = path.basename(file, path.extname(file));
                        Handlebars.registerPartial(partialName, data);
                        Handlebars.registerHelper('xif', (v1, operator, v2, options) => {

                            switch (operator) {
                                case '==':
                                    return (v1 == v2) ? options.fn(this) : options.inverse(this);
                                case '===':
                                    return (v1 === v2) ? options.fn(this) : options.inverse(this);
                                case '<':
                                    return (v1 < v2) ? options.fn(this) : options.inverse(this);
                                case '<=':
                                    return (v1 <= v2) ? options.fn(this) : options.inverse(this);
                                case '>':
                                    return (v1 > v2) ? options.fn(this) : options.inverse(this);
                                case '>=':
                                    return (v1 >= v2) ? options.fn(this) : options.inverse(this);
                                case '&&':
                                    return (v1 && v2) ? options.fn(this) : options.inverse(this);
                                case '||':
                                    return (v1 || v2) ? options.fn(this) : options.inverse(this);
                                default:
                                    return options.inverse(this);
                            }
                        });

                        Handlebars.registerHelper('counter', function(index) {
                            return index + 1;
                        });

                        Handlebars.registerHelper('version', function(filename) {
                            return `${filename}?${Date.now()}`;
                        });

                    });
                },
                tags: {
                    color: function() {
                        const matches = (this.tag.description).match(/\[(.*?)\]/);

                        if (matches) {
                            const section = this.sections[matches[1]];

                            if (section) {
                                section.colors = section.colors || [];
                                section.colors.push({
                                    name: this.tag.name,
                                    hex: this.tag.type,
                                    rgb: hexToRgb(this.tag.type)
                                });
                            }
                        }
                    }
                }
            }))
            .pipe(gulp.dest(dest));
    };
}

function hexToRgb(hex) {
    const strippedHex = hex.replace(/[^0-9A-F]/gi, '');
    const bigint = parseInt(strippedHex, 16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;

    return `${r}, ${g}, ${b}`;
}
