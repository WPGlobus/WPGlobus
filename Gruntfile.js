/*jslint node: true */
/*global require*/
'use strict';

module.exports = function (grunt) {

    var
        pkgJson,
        pkgName,
        pathIncludes = 'includes',
        pathLanguages = 'languages',
        pathCSS,
        pathJS,
        potFile,
        pathCSS_options_fields,
        pathJS_options_fields,
        pathComposer = '/cygwin64/usr/local/bin/composer',
        execSync,
        poFilePaths;


    pkgJson = require('./package.json');
    pkgName = pkgJson.name;


    pathCSS = pathIncludes + '/css';
    pathJS = pathIncludes + '/js';

    potFile = pathLanguages + '/' + pkgName + '.pot';
    poFilePaths = grunt.file.expand(pathLanguages + '/*.po');

    //noinspection JSUnresolvedVariable
    execSync = require('child_process').execSync;

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

        /**
         * @link https://www.npmjs.com/package/grunt-wp-readme-to-markdown
         */
        wp_readme_to_markdown: {
            options: {
                screenshot_url: 'https://ps.w.org/wpglobus/assets/{screenshot}.png'
            },
            main   : {
                files: {
                    'README.md': 'readme.txt'
                }
            }
        },

        /**
         * @link https://github.com/gruntjs/grunt-contrib-uglify
         */
        uglify: {
            main          : {
                files: [{
                    expand: true,
                    cwd   : pathJS + '/',
                    src   : ['*.js', '!*.min.js'],
                    dest  : pathJS + '/',
                    ext   : '.min.js'
                }]
            },
            options_fields: {
                files: [{
                    src    : [
                        pathJS_options_fields + '/**/*.js',
                        '!' + pathJS_options_fields + '/**/*.min.js'
                    ],
                    ext    : '.min.js',
                    expand : true,
                    flatten: false
                }]
            }
        },

        /**
         * @link https://github.com/gruntjs/grunt-contrib-less
         */
        less: {
            main          : {
                options: {
                    paths            : [pathCSS],
                    sourceMap        : true,
                    sourceMapBasepath: pathCSS,
                    sourceMapURL     : 'wpglobus.css.map'
                },
                files  : {
                    "includes/css/wpglobus.css": pathCSS + '/' + "wpglobus.less"
                }
            },
            admin         : {
                options: {
                    paths            : [pathCSS],
                    sourceMap        : true,
                    sourceMapBasepath: pathCSS,
                    sourceMapURL     : 'wpglobus-admin.css.map'
                },
                files  : {
                    "includes/css/wpglobus-admin.css": pathCSS + '/' + "wpglobus-admin.less"
                }
            },
            tabs          : {
                options: {
                    paths            : [pathCSS],
                    sourceMap        : true,
                    sourceMapBasepath: pathCSS,
                    sourceMapURL     : 'wpglobus-admin-tabs.css.map'
                },
                files  : {
                    "includes/css/wpglobus-admin-tabs.css": pathCSS + '/' + "wpglobus-admin-tabs.less"
                }
            },
            dialogui      : {
                options: {
                    paths            : [pathCSS],
                    sourceMap        : true,
                    sourceMapBasepath: pathCSS,
                    sourceMapURL     : 'wpglobus-dialog-ui.css.map'
                },
                files  : {
                    "includes/css/wpglobus-dialog-ui.css": pathCSS + '/' + "wpglobus-dialog-ui.less"
                }
            },
            options_fields: {
                options: {
                    sourceMap: false // Does not work properly with globs
                },
                files  : [{
                    src    : [pathCSS_options_fields + '/**/*.less'],
                    ext    : '.css',
                    expand : true,
                    flatten: false
                }]
            }
        },

        /**
         * @link https://www.npmjs.org/package/grunt-contrib-cssmin
         */
        cssmin: {
            main          : {
                options: {
                    keepSpecialComments: 0
                },
                expand : true,
                cwd    : pathCSS + '/',
                src    : ['*.css', '!*.min.css'],
                dest   : pathCSS + '/',
                ext    : '.min.css'
            },
            options_fields: {
                options: {
                    keepSpecialComments: 0
                },
                files  : [{
                    src    : [pathCSS_options_fields + '/**/*.css'],
                    ext    : '.min.css',
                    expand : true,
                    flatten: false
                }]
            }
        },

        makepot: {
            target: {
                options: {
                    mainFile       : pkgName + '.php',                     // Main project file.
                    potHeaders     : {
                        poedit                 : true,                 // Includes common Poedit headers.
                        'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
                    },                                // Headers to add to the generated POT file.
                    processPot     : function (pot) {
                        pot.headers['report-msgid-bugs-to'] = 'http://www.wpglobus.com/pg/contact-us/';
                        pot.headers['language-team'] = 'The WPGlobus Team <support@wpglobus.com>';
                        pot.headers['last-translator'] = pot.headers['language-team'];
                        delete pot.headers['x-generator'];
                        return pot;
                    },
                    type           : 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
                    updateTimestamp: true,             // Whether the POT-Creation-Date should be updated without other changes.
                    updatePoFiles  : false              // Whether to update PO files in the same directory as the POT file.
                }
            }
        },

        exec: {
            tx_push_s: {
                cmd: 'tx push -s'
            },
            tx_pull  : { // Pull Transifex translation - grunt exec:tx_pull
                cmd: 'tx pull -a -f --mode=translator' // Change the percentage with --minimum-perc=value
            },
            cpzu     : { // Install dependencies with Composer
                cmd: 'php ' + pathComposer + ' update --no-ansi --no-autoloader'
            }
        },

        replace: {
            readme_md: {
                overwrite   : true,
                src         : ['README.md'],
                replacements: [
                    {
                        from: '![Multilingual WooCommerce store powered by [WooCommerce WPGlobus](http://www.wpglobus.com/shop/extensions/woocommerce-wpglobus/).](https://ps.w.org/wpglobus/assets/screenshot-8.png)',
                        to  : '![Multilingual WooCommerce store powered by WooCommerce WPGlobus.](https://ps.w.org/wpglobus/assets/screenshot-8.png)'
                    },
                    {
                        from: '# WPGlobus - Multilingual Everything! #',
                        to  : '[![Latest Stable Version](https://poser.pugx.org/wpglobus/wpglobus/v/stable)](https://packagist.org/packages/wpglobus/wpglobus) [![Total Downloads](https://poser.pugx.org/wpglobus/wpglobus/downloads)](https://packagist.org/packages/wpglobus/wpglobus) [![Latest Unstable Version](https://poser.pugx.org/wpglobus/wpglobus/v/unstable)](https://packagist.org/packages/wpglobus/wpglobus) [![License](https://poser.pugx.org/wpglobus/wpglobus/license)](https://packagist.org/packages/wpglobus/wpglobus)\n\n# WPGlobus - Multilingual Everything! #'
                    }
                ]
            },
            version  : {
                overwrite   : true,
                src         : [pkgName + '.php'],
                replacements: [
                    {
                        from: / \* Version: .+/,
                        to  : ' * Version: <%= pkg.version %>'
                    },
                    {
                        from: /define\( 'WPGLOBUS_VERSION', '[^']+' \);/,
                        to  : "define( 'WPGLOBUS_VERSION', '<%= pkg.version %>' );"
                    }
                ]
            }
        },


        /**
         * @link https://github.com/gruntjs/grunt-contrib-watch
         */
        watch: {
            files  : [
                'Gruntfile.js',
                pathCSS + '/*.less',
                pathJS + '/*.js',
                pathCSS_options_fields + '/**/*.less',
                pathJS_options_fields + '/**/*.js'
            ],
            tasks  : ['less', 'cssmin', 'uglify'],
            options: {
                spawn: false
            }
        }
    });

    grunt.registerTask('po', 'Merge POT into individual PO files', function () {
        poFilePaths.forEach(function (poFile) {
            grunt.log.writeln("Making PO: " + poFile);
            execSync('msgmerge -v --backup=none --no-fuzzy-matching --update ' + poFile + ' ' + potFile);
        });
    });

    grunt.registerTask('mo', 'Compile PO to MO files', function () {
        poFilePaths.forEach(function (poFile) {
            var moFile = poFile.replace(/\.po$/, '.mo');
            grunt.log.writeln("Making MO: " + moFile);
            execSync('msgfmt -v -o ' + moFile + ' ' + poFile);
        });
    });

    grunt.registerTask('pomo', [
        'makepot',
        //'exec:tx_push_s',
        //'exec:tx_pull',
        'po',
        'mo'
    ]);

    grunt.registerTask('readme_md', ['wp_readme_to_markdown', 'replace:readme_md']);

    grunt.registerTask('dist', [
        'exec:cpzu',
        'replace:version',
        'pomo',
        'readme_md',
        'less',
        'cssmin',
        'uglify'
    ]);

    // Default task(s).
    grunt.registerTask('default', ['watch']);

};
