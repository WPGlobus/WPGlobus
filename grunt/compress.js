/* jshint node:true */
/**
 * @link https://github.com/gruntjs/grunt-contrib-compress
 */
module.exports = {
    'dist': {
        options: {
            archive: '<%= package.tivwp_config.path.dist %>/<%= package.name %>-<%= package.version %>.zip'
        },
        files  : [{
            expand: true,
            src   : [
                '**/*',
                '!Gruntfile.js',
                '!grunt/**',
                '!bower.json',
                '!composer.json',
                '!composer.lock',
                '!package.json',
                '!unit-tests/**',
                '!vendor/bin/**',
                '!vendor/composer/**',
                '!node_modules/**',
                '!npm-debug.log',
                '!.git/**',
                '!.gitignore',
                '!tests/**',
                '!bin/**',
                '!phpunit.xml',
                '!travis.yml',
                '!.tx'
            ],
            dest  : './<%= package.name %>'
        }]
    }
};
