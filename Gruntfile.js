/*jslint node: true */
module.exports = function (grunt) {

    'use strict';

    var
        bannerTemplate = '/* <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %> */',
        pathIncludes = 'includes',
        pathCSS,
        pathJS
        ;
    pathCSS = pathIncludes + '/css';
    pathJS = pathIncludes + '/js';

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        /**
         * @link https://github.com/gruntjs/grunt-contrib-uglify
         */
        uglify: {
            all: {
                //options: {
                //    sourceMap: true
                //},
                files: [{
                    expand: true,
                    cwd: pathJS + '/',
                    src: ['*.js', '!*.min.js'],
                    dest: pathJS + '/',
                    ext: '.min.js'
                }]
            }
        },

        /**
         * @link https://github.com/gruntjs/grunt-contrib-less
         */
        less: {
            all: {
                options: {
                    banner: bannerTemplate,
                    paths: [pathCSS],
                    sourceMap: true
                    //sourceMapURL: pathCSS + '/style.css.map',
                    //sourceMapFilename: pathCSS + '/style.css.map'
                },
                files: [{
                    expand: true,
                    cwd: pathCSS + '/',
                    src: ['*.less'],
                    dest: pathCSS + '/',
                    ext: '.css'
                }]
            }
        },

        /**
         * @link https://www.npmjs.org/package/grunt-contrib-cssmin
         */
        cssmin: {
            all: {
                options: {
                    keepSpecialComments: 0
                },
                expand: true,
                cwd: pathCSS + '/',
                src: ['*.css', '!*.min.css'],
                dest: pathCSS + '/',
                ext: '.min.css'
            }
        },

        /**
         * @link https://github.com/gruntjs/grunt-contrib-watch
         */
        watch: {
            files: [pathCSS + '/*.less', pathJS + '/*.js'],
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

    // Default task(s).
    grunt.registerTask('default', ['watch:child']);

};
