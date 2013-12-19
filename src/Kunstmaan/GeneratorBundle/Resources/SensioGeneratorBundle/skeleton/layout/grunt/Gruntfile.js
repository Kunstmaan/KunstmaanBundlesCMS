var _ = require('underscore');

module.exports = function (grunt) {
    "use strict";

    var {{ bundle.getName() }};

    var resourcesPath = 'src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/';

    {{ bundle.getName() }} = {
        'destination':  'web/frontend/',
        'js':           [resourcesPath+'public/**/*.js', '!'+ resourcesPath+'public/vendor/**/*.js', 'Gruntfile.js'],
        'all_scss':     [resourcesPath+'public/scss/**/*.scss'],
        'scss':         [resourcesPath+'public/scss/style.scss', resourcesPath+'public/scss/legacy/ie/ie7.scss', resourcesPath+'public/scss/legacy/ie/ie8.scss'],
        'twig':         [resourcesPath+'views/**/*.html.twig'],
        'img':          [resourcesPath+'public/img/**/*.{png,jpg,jpeg,gif,webp}'],
        'svg':          [resourcesPath+'public/img/**/*.svg']
    };

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        watch: {
            {{ bundle.getName() }}Js: {
                files: {{ bundle.getName() }}.js,
                tasks: 'jshint:{{ bundle.getName() }}',
                options: {
                    nospawn: true
                }
            },
            {{ bundle.getName() }}Scss: {
                files: {{ bundle.getName() }}.scss,
                tasks: 'sass'
            },
            {{ bundle.getName() }}Images: {
                files: {{ bundle.getName() }}.img,
                tasks: ['imagemin:{{ bundle.getName() }}'],
                options: {
                    event: ['added', 'changed']
                }
            },
            {{ bundle.getName() }}Svg: {
                files: {{ bundle.getName() }}.svg,
                tasks: ['svg2png:{{ bundle.getName() }}'],
                options: {
                    event: ['added', 'changed']
                }
            },
            livereload: {
                files: [{{ bundle.getName() }}.js, {{ bundle.getName() }}.twig, {{ bundle.getName() }}.img, {{ bundle.getName() }}.svg, 'web/frontend/css/style.css'],
                options: {
                    livereload: true
                }
            }
        },

        jshint: {
            options: {
                camelcase: true,
                curly: true,
                eqeqeq: true,
                eqnull: true,
                forin: true,
                indent: 4,
                trailing: true,
                undef: true,
                browser: true,
                devel: true,
                node: true,
                globals: {
                    jQuery: true,
                    $: true
                }
            },
            {{ bundle.getName() }}: {
                files: {
                    src: {{ bundle.getName() }}.js
                }
            }
        },

        imagemin: {
            {{ bundle.getName() }}: {
                options: {
                    optimizationLevel: 3,
                    progressive: true
                },
                files: [{
                    expand: true,
                    cwd: 'src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/public/img',
                    src: '**/*.{png,jpg,jpeg,gif,webp}',
                    dest: 'src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/public/img'
                }]
            }
        },

        svg2png: {
            {{ bundle.getName() }}: {
                files: [{
                    src: {{ bundle.getName() }}.svg
                }]
            }
        },


        modernizr: {
            devFile: 'remote',
                outputFile: {{ bundle.getName() }}.destination + 'js/modernizr-custom.js',
                files: _.union({{ bundle.getName() }}.js, {{ bundle.getName() }}.all_scss, {{ bundle.getName() }}.twig),
                parseFiles: true,
                extra: {
                'shiv' : true,
                    'printshiv' : false,
                    'load' : true,
                    'mq' : false,
                    'cssclasses' : true
            },
            extensibility: {
                'addtest' : false,
                    'prefixed' : false,
                    'teststyles' : false,
                    'testprops' : false,
                    'testallprops' : false,
                    'hasevents' : false,
                    'prefixes' : false,
                    'domprefixes' : false
            }
        },

        sass: {
            {{ bundle.getName() }}: {
                options: {
                    style: 'compressed'
                },
                files: {
                    'web/frontend/css/style.css': resourcesPath+'public/scss/style.scss',
                    'web/frontend/css/ie8.css': resourcesPath+'public/scss/legacy/ie/ie8.scss',
                    'web/frontend/css/ie7.css': resourcesPath+'public/scss/legacy/ie/ie7.scss'
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-svg2png');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks("grunt-modernizr");
    grunt.loadNpmTasks('grunt-notify');
    grunt.loadNpmTasks('grunt-contrib-sass');

    grunt.registerTask('default', ['watch']);
    grunt.registerTask('build', ['sass', 'modernizr']);
};
