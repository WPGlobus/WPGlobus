/*jslint node: true */
module.exports = function (grunt) {

    'use strict';

    var
    //bannerTemplate = '/* <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %> */',
        pathIncludes = 'includes',
        pathCSS,
        pathCSS_field_table,
        pathJS,
        pathJS_field_table
        ;
    pathCSS = pathIncludes + '/css';
    pathJS = pathIncludes + '/js';

    /**
     * "Table" field for Redux
     */
    pathCSS_field_table = pathIncludes + '/options/fields/table';
    pathJS_field_table = pathCSS_field_table;

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

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
            field_table: {
                files: [{
                    expand: true,
                    cwd: pathJS_field_table + '/',
                    src: ['*.js', '!*.min.js'],
                    dest: pathJS_field_table + '/',
                    ext: '.min.js'
                }]
            }
        },

        /**
         * @link https://github.com/gruntjs/grunt-contrib-less
         */
        less: {
            admin: {
                options: {
                    //banner: bannerTemplate,
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
                    //banner: bannerTemplate,
                    paths: [pathCSS],
                    sourceMap: true,
                    sourceMapBasepath: pathCSS,
                    sourceMapURL: 'wpglobus-admin-tabs.css.map'
                },
                files: {
                    "includes/css/wpglobus-admin-tabs.css": pathCSS + '/' + "wpglobus-admin-tabs.less"
                }
            },
            flags: {
                options: {
                    //banner: bannerTemplate,
                    paths: [pathCSS],
                    sourceMap: true,
                    sourceMapBasepath: pathCSS,
                    sourceMapURL: 'wpglobus-flags.css.map'
                },
                files: {
                    "includes/css/wpglobus-flags.css": pathCSS + '/' + "wpglobus-flags.less"
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
                msgid_bugs_address: 'translation@wpglobus.com',
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
                src: ['includes/**/*.php'], //Parse all php files
                expand: true
            }
        },

        /**
         * @link https://github.com/gruntjs/grunt-contrib-watch
         */
        watch: {
            files: [
                pathCSS + '/*.less',
                pathCSS_field_table + '/*.less',
                pathJS + '/*.js',
                pathJS_field_table + '/*.js'
            ],
            tasks: ['less', 'cssmin', 'uglify'],
            options: {
                spawn: false
            }
        }
    });

    // Load the Grunt plugins
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-pot');

    /**
     * Had to write this because "pot" does not do msgmerge correctly
     * @link https://github.com/stephenharris/grunt-pot/issues/11
     */
    grunt.registerTask('after-pot', 'To run after the "pot" task', function () {
        var execSync = require('child_process').execSync;
        var potFile = 'languages/wpglobus.pot';
        var poFilePaths = grunt.file.expand('languages/*.po');
        poFilePaths.forEach(function (poFile) {
            grunt.log.writeln("msgmerge-ing " + poFile);
            execSync('msgmerge -vU ' + poFile + ' ' + potFile);
        });
    });

    grunt.registerTask('pomo', ['pot', 'after-pot']);

    // To run all tasks - same list as for `watch`
    grunt.registerTask('dist', ['less', 'cssmin', 'uglify', 'pomo']);

    // Default task(s).
    grunt.registerTask('default', ['watch']);

};
