/**
 * @link https://github.com/gruntjs/grunt-contrib-uglify
 */
module.exports = {
    all: {
        files: [{
            expand: true,
            cwd: "./",
            src: [
                "<%= cfg.path.js %>/**/*.js",
                "!**/*.min.js"
            ],
            dest: "./",
            ext: ".min.js"
        }]
    }
};
