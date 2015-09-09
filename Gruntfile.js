/*jslint node: true */
'use strict';

module.exports = function (grunt) {

    var
    //bannerTemplate = '/* <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %> */',
        pathIncludes = 'includes',
        pathCSS,
        pathCSS_options_fields,
        pathJS,
        pathJS_options_fields
        ;
    pathCSS = pathIncludes + '/css';
    pathJS = pathIncludes + '/js';

    /**
     * Custom Redux fields
     */
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
                        '!' + pathJS_options_fields + '/**/*.min.js'
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
            options_fields: {
                options: {
                    sourceMap: false, // Does not work properly with globs
                },
                files: [{
                    src: [pathCSS_options_fields + '/**/*.less'],
                    ext: '.css',
                    expand: true,
                    flatten: false,
                }]
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
            options_fields: {
                options: {
                    keepSpecialComments: 0
                },
                files: [{
                    src: [pathCSS_options_fields + '/**/*.css'],
                    ext: '.min.css',
                    expand: true,
                    flatten: false,
                }]
            }
        },

        makepot: {
            target: {
                options: {
                    mainFile: 'wpglobus.php',                     // Main project file.
                    potHeaders: {
                        poedit: true,                 // Includes common Poedit headers.
                        'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
                    },                                // Headers to add to the generated POT file.
                    processPot: function( pot, options ) {
                        pot.headers['report-msgid-bugs-to'] = 'http://www.wpglobus.com/pg/contact-us/';
                        pot.headers['language-team'] = 'The WPGlobus Team <support@wpglobus.com>';
                        pot.headers['last-translator'] = pot.headers['language-team'];
                        delete pot.headers['x-generator'];
                        return pot;
                    },
                    type: 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
                    updateTimestamp: true,             // Whether the POT-Creation-Date should be updated without other changes.
                    updatePoFiles: false              // Whether to update PO files in the same directory as the POT file.
                }
            }
        },

        pot: {
            options: {
                encoding: 'UTF-8',
                msgid_bugs_address: 'support@wpglobus.com',
                copyright_holder: '<%= grunt.template.today("yyyy") %>, WPGlobus',
                comment_tag: 'TRANSLATORS:',
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

        replace: {
            pot: {
                overwrite: true,
                src: ['languages/wpglobus.pot'],             // source files array (supports minimatch)
                replacements: [
                    {
                        from: 'SOME DESCRIPTIVE TITLE',                   // string replacement
                        to: 'Translations for WPGlobus plugin'
                    },
                    {
                        from: '# FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.',
                        to: ''
                    },
                    {
                        from: '# This file is distributed under the same license as the PACKAGE package.',
                        to: ''
                    },
                    {
                        from: '# Copyright (C) YEAR',
                        to: '# Copyright (c)'
                    }
                ]
            },
            readme_md: {
                overwrite: true,
                src: ['README.md'],
                replacements: [
                    {
                        from: 'http://s.wordpress.org/extend/plugins/wpglobus---multilingual-everything!/',
                        to: 'https://ps.w.org/wpglobus/assets/'
                    },
                    {
                        from: '![Multilingual WooCommerce store powered by [WooCommerce WPGlobus](http://www.wpglobus.com/shop/extensions/woocommerce-wpglobus/).](https://ps.w.org/wpglobus/assets/screenshot-8.png)',
                        to: '![Multilingual WooCommerce store powered by WooCommerce WPGlobus.](https://ps.w.org/wpglobus/assets/screenshot-8.png)'
                    }
                ]
            },
            version: {
                overwrite: true,
                src: ['wpglobus.php'],
                replacements: [
                    {
                        from: / \* Version: [0-9\.]+/,
                        to: ' * Version: <%= pkg.version %>'
                    },
                    {
                        from: /define\( 'WPGLOBUS_VERSION', '[0-9\.]+' \);/,
                        to: "define( 'WPGLOBUS_VERSION', '<%= pkg.version %>' );"
                    }
                ]
            }
        },


        /**
         * @link https://github.com/gruntjs/grunt-contrib-watch
         */
        watch: {
            files: [
                'Gruntfile.js',
                pathCSS + '/*.less',
                pathCSS_options_fields + '/**/*.less',
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
    grunt.registerTask('po', 'Making PO files', function () {
        var execSync = require('child_process').execSync;
        var potFile = 'languages/wpglobus.pot';
        var poFilePaths = grunt.file.expand('languages/*.po');
        poFilePaths.forEach(function (poFile) {
            var moFile = poFile.replace(/\.po$/, '.mo');
            grunt.log.writeln("Making PO: " + poFile);
            execSync('msgmerge -v --backup=none --no-fuzzy-matching --update ' + poFile + ' ' + potFile);
        });
    });

    grunt.registerTask('mo', 'Compile PO to MO files', function () {
        var execSync = require('child_process').execSync;
        var potFile = 'languages/wpglobus.pot';
        var poFilePaths = grunt.file.expand('languages/*.po');
        poFilePaths.forEach(function (poFile) {
            var moFile = poFile.replace(/\.po$/, '.mo');
            grunt.log.writeln("Making MO: " + moFile);
            execSync('msgfmt -v -o ' + moFile + ' ' + poFile);
        });
    });

    //grunt.registerTask('makepot', ['pot', 'replace:pot']);

    //grunt.registerTask('pomo', ['makepot', 'po', 'mo']);

    grunt.registerTask('readme_md', ['wp_readme_to_markdown', 'replace:readme_md']);

    // To run all tasks - same list as for `watch`
    grunt.registerTask('dist', ['readme_md', 'less', 'cssmin', 'uglify', 'replace:version']);

    // Default task(s).
    grunt.registerTask('default', ['watch']);

};
