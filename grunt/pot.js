/**
 * @link https://www.npmjs.com/package/grunt-pot
 */
module.exports = {
    all: {
        options: {
            package_name: "\"<%= package.title %>\"",
            copyright_holder: "TIV.NET INC.",
            text_domain: "<%= cfg.text_domain %>",
            dest: "<%= cfg.path.languages %>/",
            encoding: "UTF-8",
            keywords: ["__", "_e", "_x:1,2c", "_ex:1,2c", "_n:1,2", "_nx:1,2,4c", "_n_noop:1,2", "_nx_noop:1,2,3c", "esc_attr__", "esc_html__", "esc_attr_e", "esc_html_e", "esc_attr_x:1,2c", "esc_html_x:1,2c"],
            msgid_bugs_address: "support@wpglobus.com",
            msgmerge: true
        },
        files: [{
            expand: true,
            cwd: ".",
            src: [
                "wpglobus.php",
                "includes/**/*.php",
                "!includes/vendor/**/*.php"
            ]
        }]
    }
};
