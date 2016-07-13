/* jshint node:true */
/**
 */
module.exports = {
    all: {
        options: {
            pot_file: '<%= package.tivwp_config.path.languages %>/<%= package.name %>.pot',
            do_mo: true
        },
        files: [{
            expand: true,
            cwd   : '<%= package.tivwp_config.path.languages %>/',
            src   : ["*.po"]
        }]
    }
};
