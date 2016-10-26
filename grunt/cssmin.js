/**
 * @link https://www.npmjs.org/package/grunt-contrib-cssmin
 */
module.exports = {
    main: {
        options: {
            keepSpecialComments: 0
        },
        files: [{
            src: [
                "<%= cfg.path.css %>/**/*.css",
                "!**/*.min.css"
            ],
            ext: ".min.css",
            expand: true,
            flatten: false
        }]
    }
};
