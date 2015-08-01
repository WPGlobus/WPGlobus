/*jslint node: true */
'use strict';

module.exports = function (grunt) {

    var
    //bannerTemplate = '/* <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %> */',
        pathIncludes = 'includes',
        pathCSS,
        pathCSS_field_table,
        pathCSS_options_fields,
        pathJS,
        pathJS_options_fields
        ;
    pathCSS = pathIncludes + '/css';
    pathJS = pathIncludes + '/js';

    /**
     * Custom Redux fields
     */
    pathCSS_field_table = pathIncludes + '/options/fields/table';
    pathCSS_options_fields = pathIncludes + '/options/fields';
    pathJS_options_fields = pathCSS_options_fields;

    /**
     * Auto-load grunt tasks
     * @link https://www.npmjs.com/package/load-grunt-tasks
     */
    require('load-grunt-tasks')(grunt);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        wp_readme_to_markdown: {
            main: {
                files: {
                    'readme.md': 'readme.txt'
                }
            }
        },

        /**
         * @link https://github.com/gruntjs/grunt-contrib-uglify
         */
        uglify: {
            main: {
                files: [{
                    expand: true,
                    cwd: pathJS + '/',
                    src: ['*.js', '!*.min.js'],
                    dest: pathJS + '/',
                    ext: '.min.js'
                }]
            },
            options_fields: {
                files: [{
                    src: [
                        pathJS_options_fields + '/**/*.js',
                        '!' + pathJS_options_fields + '/options/fields/**/*.min.js'
                    ],
                    ext: '.min.js',
                    expand: true,
                    flatten: false,
                }]
            }
        },

        /**
         * @link https://github.com/gruntjs/grunt-contrib-less
         */
        less: {
            main: {
                options: {
                    paths: [pathCSS],
                    sourceMap: true,
                    sourceMapBasepath: pathCSS,
                    sourceMapURL: 'wpglobus.css.map'
                },
                files: {
                    "includes/css/wpglobus.css": pathCSS + '/' + "wpglobus.less"
                }
            },
            admin: {
                options: {
                    paths: [pathCSS],
                    sourceMap: true,
                    sourceMapBasepath: pathCSS,
                    sourceMapURL: 'wpglobus-admin.css.map'
                },
                files: {
                    "includes/css/wpglobus-admin.css": pathCSS + '/' + "wpglobus-admin.less"
                }
            },
            tabs: {
                options: {
                    paths: [pathCSS],
                    sourceMap: true,
                    sourceMapBasepath: pathCSS,
                    sourceMapURL: 'wpglobus-admin-tabs.css.map'
                },
                files: {
                    "includes/css/wpglobus-admin-tabs.css": pathCSS + '/' + "wpglobus-admin-tabs.less"
                }
            },
            dialogui: {
                options: {
                    paths: [pathCSS],
                    sourceMap: true,
                    sourceMapBasepath: pathCSS,
                    sourceMapURL: 'wpglobus-dialog-ui.css.map'
                },
                files: {
                    "includes/css/wpglobus-dialog-ui.css": pathCSS + '/' + "wpglobus-dialog-ui.less"
                }
            },
            field_table: {
                options: {
                    paths: [pathCSS_field_table],
                    sourceMap: true,
                    sourceMapBasepath: pathCSS_field_table,
                    sourceMapURL: 'field_table.css.map'
                },
                files: {
                    "includes/options/fields/table/field_table.css": pathCSS_field_table + '/' + "field_table.less"
                }
            }
        },

        /**
         * @link https://www.npmjs.org/package/grunt-contrib-cssmin
         */
        cssmin: {
            main: {
                options: {
                    keepSpecialComments: 0
                },
                expand: true,
                cwd: pathCSS + '/',
                src: ['*.css', '!*.min.css'],
                dest: pathCSS + '/',
                ext: '.min.css'
            },
            field_table: {
                options: {
                    keepSpecialComments: 0
                },
                expand: true,
                cwd: pathCSS_field_table + '/',
                src: ['*.css', '!*.min.css'],
                dest: pathCSS_field_table + '/',
                ext: '.min.css'
            }
        },

        pot: {
            options: {
                encoding: 'UTF-8',
                msgid_bugs_address: 'support@wpglobus.com',
                msgmerge: false,
                text_domain: 'wpglobus', //Your text domain. Produces my-text-domain.pot
                dest: 'languages/', //directory to place the pot file
                keywords: [ //WordPress localisation functions
                    '__:1',
                    '_e:1',
                    '_x:1,2c',
                    'esc_html__:1',
                    'esc_html_e:1',
                    'esc_html_x:1,2c',
                    'esc_attr__:1',
                    'esc_attr_e:1',
                    'esc_attr_x:1,2c',
                    '_ex:1,2c',
                    '_n:1,2',
                    '_nx:1,2,4c',
                    '_n_noop:1,2',
                    '_nx_noop:1,2,3c'
                ]
            },
            files: {
                src: ['includes/**/*.php', '!includes/vendor/*'], //Parse all php files except for vendor folder
                expand: true
            }
        },

        /**
         * @link https://github.com/gruntjs/grunt-contrib-watch
         */
        watch: {
            files: [
                'Gruntfile.js',
                pathCSS + '/*.less',
                pathCSS_field_table + '/*.less',
                pathJS + '/*.js',
                pathJS_options_fields + '/**/*.js'
            ],
            tasks: ['less', 'cssmin', 'uglify'],
            options: {
                spawn: false
            }
        }
    });

    /**
     * Had to write this because "pot" does not do msgmerge correctly
     * @link https://github.com/stephenharris/grunt-pot/issues/11
     */
    grunt.registerTask('after-pot', 'To run after the "pot" task', function () {
        var execSync = require('child_process').execSync;
        var potFile = 'languages/wpglobus.pot';
        var poFilePaths = grunt.file.expand('languages/*.po');
        poFilePaths.forEach(function (poFile) {
            var moFile = poFile.replace(/\.po$/, '.mo');
            grunt.log.writeln("Making PO: " + poFile);
            execSync('msgmerge -v --backup=none --no-fuzzy-matching --update ' + poFile + ' ' + potFile);
            grunt.log.writeln("Making MO: " + moFile);
            execSync('msgfmt -v -o ' + moFile + ' ' + poFile);
        });
    });

    grunt.registerTask('pomo', ['pot', 'after-pot']);

    // To run all tasks - same list as for `watch`
    grunt.registerTask('dist', ['wp_readme_to_markdown', 'less', 'cssmin', 'uglify', 'pomo']);

    // Default task(s).
    grunt.registerTask('default', ['watch']);

};
