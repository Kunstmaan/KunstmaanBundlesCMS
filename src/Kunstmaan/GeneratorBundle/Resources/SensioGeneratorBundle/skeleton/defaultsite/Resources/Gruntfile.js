module.exports = function (grunt) {
    "use strict";

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        watch: {
            scripts: {
                files: ['<%= pkg.bundlePath %>/public/js/**/*.js'],
                tasks: ['jshint'],
                options: {
                    nospawn: true,
                    event: ['added', 'changed']
                }
            },
            png: {
                files: ['<%= pkg.bundlePath %>/public/img/**/*.png'],
                tasks: ['imagemin'],
                options: {
                    event: ['added', 'changed']
                }
            },
            svg: {
                files: ['<%= pkg.bundlePath %>/public/img/**/*.svg'],
                tasks: ['svg2png'],
                options: {
                    event: ['added', 'changed']
                }
            },
            modernizr: {
                files: [
                    '<%= pkg.bundlePath %>/public/**/*.js',
                    '<%= pkg.bundlePath %>/public/**/*.scss',
                    '<%= pkg.bundlePath %>/views/**/*.html.twig'
                ],
                tasks: ['modernizr']
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
                unused: true,
                browser: true,
                devel: true,
                node: true,
                globals: {
                    jQuery: true,
                    $: true
                }
            },
            all: ['Gruntfile.js', '<%= pkg.bundlePath %>/public/js']
        },

        imagemin: {
            png: {
                options: {
                    optimizationLevel: 3,
                    progressive: true
                },
                files: [{
                    expand: true,
                    cwd: '<%= pkg.bundlePath %>/public/img',
                    src: '**/*.png',
                    dest: '<%= pkg.bundlePath %>/public/img',
                    ext: '.png'
                }]
            }
        },

        svg2png: {
            all: {
                files: [
                    { src: ['<%= pkg.bundlePath %>/public/img/**/*.svg'] }
                ]
            }
        },

        modernizr: {
            devFile: 'remote',
            outputFile: '<%= pkg.bundlePath %>/public/vendor/modernizr/modernizr-custom.js',
            parseFiles: true,
            files: ['<%= pkg.bundlePath %>/public/**/*.*', '<%= pkg.bundlePath %>/views/**/*.html.twig'],
            extra: {
                "shiv" : true,
                "printshiv" : false,
                "load" : true,
                "mq" : false,
                "cssclasses" : true
            },
            extensibility: {
                "addtest" : false,
                "prefixed" : false,
                "teststyles" : false,
                "testprops" : false,
                "testallprops" : false,
                "hasevents" : false,
                "prefixes" : false,
                "domprefixes" : false
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks("grunt-modernizr");
    grunt.loadNpmTasks('grunt-svg2png');
    grunt.loadNpmTasks('grunt-notify');

    grunt.registerTask('default', ['watch']);
};