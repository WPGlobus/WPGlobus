/**
 * Transifex commands:
 * ===================
 * To push POT:
 *      tx push -s
 * To push translation (i.e. `ar`):
 *      tx push -t -l ar
 * To pull a translation:
 *      tx pull -l ar --mode=translator
 * To pull all:
 *      tx pull -a -f --mode=translator
 */

module.exports = {
    tx_push_s: {
        cmd: "tx push -s"
    },
    tx_pull: { // Pull Transifex translation - grunt exec:tx_pull
        cmd: "tx pull -a -f --mode=translator" // Change the percentage with --minimum-perc=value
    },
    cpzu: { // Install dependencies with Composer
        cmd: "php <%= cfg.path.composer %> update -q --no-autoloader"
    },
    zip: {
        cmd: "cd .. && zip -FSrT <%= cfg.path.dist %>/<%= package.name %>-<%= package.version %>.zip <%= package.name %> -x@<%= package.name %>/zip_exclude.txt"
    }
};
