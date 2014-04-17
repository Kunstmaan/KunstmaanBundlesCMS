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
            {{ bundle.getName() }}Scss: {
                files: {{ bundle.getName() }}.all_scss,
                tasks: ['sass', 'cmq', 'cssmin']
            },
            {{ bundle.getName() }}Js: {
                files: {{ bundle.getName() }}.js,
                tasks: ['uglify', 'concat']
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
                tasks: ['svg2png:{{ bundle.getName() }}', 'svgmin'],
                options: {
                    event: ['added', 'changed']
                }
            },
            livereload: {
                files: ['web/frontend/css/style.min.css', 'web/frontend/js/footer.min.js'],
                options: {
                    livereload: true
                }
            }
        },

        sass: {
            {{ bundle.getName() }}: {
                options: {
                    style: 'compressed'
                },
                files: {
                    'web/frontend/.temp/css/style.css': resourcesPath+'public/scss/style.scss',
                    'web/frontend/.temp/css/ie8.css': resourcesPath+'public/scss/legacy/ie/ie8.scss',
                    'web/frontend/.temp/css/ie7.css': resourcesPath+'public/scss/legacy/ie/ie7.scss'
                }
            }
        },

        cmq: {
            {{ bundle.getName() }}: {
                files: {
                    'web/frontend/.temp/css/': 'web/frontend/.temp/css/style.css'
                }
            }
        },

        cssmin: {
            {{ bundle.getName() }}: {
                files: {
                    'web/frontend/css/style.min.css': [
                        'web/vendor/flexslider/flexslider.css',
                        'web/frontend/.temp/css/style.css'
                    ],
                    'web/frontend/css/ie8.min.css': [
                        'web/vendor/flexslider/flexslider.css',
                        'web/frontend/.temp/css/ie8.css'
                    ],
                    'web/frontend/css/ie7.min.css': [
                        'web/vendor/flexslider/flexslider.css',
                        'web/frontend/.temp/css/ie7.css'
                    ]
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

        uglify: {
            analytics: {
                files: {
                    'web/frontend/js/analytics.min.js': [
                        'vendor/kunstmaan/seo-bundle/Kunstmaan/SeoBundle/Resources/public/js/analytics.js'
                    ]
                }
            },
            vendors: {
                options: {
                    mangle: {
                        except: ['jQuery']
                    }
                },
                files: {
                    'web/frontend/.temp/js/vendors.min.js': [
                        'web/vendor/jquery/dist/jquery.js',
                        'web/vendor/sass-bootstrap/js/modal.js',
                        'web/vendor/flexslider/jquery.flexslider.js',
                        'web/vendor/fitvids/jquery.fitvids.js',
                        'web/vendor/fancybox/source/jquery.fancybox.js',
                        'web/vendor/cupcake/js/navigation/jquery.navigation.js',
                    ]
                }
            },
            {{ bundle.getName() }}: {
                files: {
                    'web/frontend/.temp/js/app.min.js': [resourcesPath+'public/js/**/*.js']
                }
            }
        },

        concat: {
            js: {
                src: [
                    'web/frontend/js/modernizr-custom.js',
                    'web/frontend/.temp/js/vendors.min.js',
                    'web/frontend/.temp/js/app.min.js'
                ],
                dest: 'web/frontend/js/footer.min.js'
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

        svgmin: {
            {{ bundle.getName() }}: {
                options: {
                    plugins: [{
                        removeViewBox: false
                    }]
                },
                files: [{
                    expand: true,
                    cwd: 'src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/public/img',
                    src: '**/*.svg',
                    dest: 'src/{{ bundle.namespace|replace({'\\':'/'}) }}/Resources/public/img'
                }]
            }
        },

        modernizr: {
            {{ bundle.getName() }}: {
                devFile: 'remote',
                parseFiles: true,
                files: {
                    src: [
                        {{ bundle.getName() }}.js,
                        {{ bundle.getName() }}.all_scss,
                        {{ bundle.getName() }}.twig
                    ]
                },
                outputFile: {{ bundle.getName() }}.destination + 'js/modernizr-custom.js',

                extra: {
                    'shiv' : false,
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
            }
        }

    });

    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-svg2png');
    grunt.loadNpmTasks('grunt-svgmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks("grunt-modernizr");
    grunt.loadNpmTasks('grunt-notify');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-combine-media-queries');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('default', ['watch']);
    grunt.registerTask('build', ['sass', 'cmq', 'cssmin', 'modernizr', 'uglify', 'concat']);
};
