/**
 * @link https://www.npmjs.com/package/grunt-tivwp-po
 */
module.exports = {
    all: {
        options: {
            pot_file: "<%= cfg.path.languages %>/<%= cfg.text_domain %>.pot",
            do_mo: true
        },
        files: [{
            expand: true,
            cwd: "<%= cfg.path.languages %>/",
            src: ["*.po"]
        }]
    }
};
