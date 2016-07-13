/* jshint node:true */
/**
 * @link https://github.com/gruntjs/grunt-contrib-less
 */
module.exports = {
    main: {
        options: {
            sourceMap: false // Does not work properly with globs
        },
        files  : [{
            src    : [
                '<%= package.tivwp_config.path.less %>/**/*.less',
                '!**/*.mixin.less'
            ],
            ext    : '.css',
            expand : true,
            flatten: false
        }]
    }
};
