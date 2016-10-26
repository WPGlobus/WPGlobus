/**
 * @link https://www.npmjs.com/package/grunt-writefile
 */
module.exports = {
    // Pointer to the latest ZIP file to be used in shell scripts.
    latest_zip: {
        options: {
            data: {
                slug: "<%= package.name %>",
                version: "<%= package.version %>"
            }
        },
        src: "<%= cfg.path.dist %>/latest.hbs",
        dest: "<%= cfg.path.dist %>/latest-<%= package.name %>.txt"
    }
};
