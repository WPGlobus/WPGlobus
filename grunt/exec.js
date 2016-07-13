/* jshint node:true */
module.exports = {
    tx_push_s: {
        cmd: 'tx push -s'
    },
    tx_pull  : { // Pull Transifex translation - grunt exec:tx_pull
        cmd: 'tx pull -a -f --mode=translator' // Change the percentage with --minimum-perc=value
    },
    cpzu     : { // Install dependencies with Composer
        cmd: 'php <%= package.tivwp_config.path.composer %> update --no-ansi --no-autoloader'
    }
};
