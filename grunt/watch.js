/**
 * @link https://github.com/gruntjs/grunt-contrib-watch
 */
module.exports = {
    files: [
        "Gruntfile.js",
        "<%= cfg.path.less %>/**/*.less"
    ],
    tasks: ["less"],
    options: {
        spawn: false
    }
};
