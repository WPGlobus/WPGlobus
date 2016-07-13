/* jshint node:true */
/**
 * @link https://github.com/gruntjs/grunt-contrib-watch
 */
module.exports = {
    files  : [
        'Gruntfile.js',
        '<%= package.tivwp_config.path.less %>/**/*.less'
    ],
    tasks  : ['less'],
    options: {
        spawn: false
    }
};
