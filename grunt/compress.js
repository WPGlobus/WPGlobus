/**
 * @link https://github.com/gruntjs/grunt-contrib-compress
 */
module.exports = {
    "dist": {
        options: {
            archive: "<%= cfg.path.dist %>/<%= package.name %>-<%= package.version %>.zip"
        },
        files: [{
            expand: true,
            src: [
                "**/*",
                "!.git/**",
                "!.gitignore",
                "!.tx",
                "!assets/**",
                "!bin/**",
                "!bower.json",
                "!CHANGELOG.md",
                "!composer.json",
                "!composer.lock",
                "!grunt/**",
                "!Gruntfile.js",
                "!node_modules/**",
                "!npm-debug.log",
                "!package.json",
                "!phpunit.xml",
                "!README.md",
                "!tests/**",
                "!travis.yml",
                "!unit-tests/**",
                "!vendor/bin/**",
                "!vendor/composer/**"
            ],
            dest: "./<%= package.name %>"
        }]
    }
};
