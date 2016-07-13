/* jshint node:true */
/**
 * @link https://github.com/gruntjs/grunt-contrib-uglify
 */
module.exports = {
    all: {
        files: [{
            expand: true,
            cwd   : './',
            src   : [
                '<%= package.tivwp_config.path.js %>/**/*.js',
                '!**/*.min.js'
            ],
            dest  : './',
            ext   : '.min.js'
        }]
    }
};
