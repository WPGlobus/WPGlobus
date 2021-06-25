/**
 * gulp-tivwp-po.js
 * Run msgmerge and msgfmt on all .po files in a folder.
 * @link https://github.com/tivnet/gulp-tivwp-po
 * @author Gregory Karpinsky
 * @copyright (c) 2018 TIV.NET INC. - All Rights Reserved.
 */

"use strict";

module.exports = function (opt) {
    const PLUGIN_NAME = "gulp-tivwp-po";
    const execSync = require("child_process").execSync;
    const through = require("through2");
    const PluginError = require('plugin-error');
    const log = require('fancy-log');

    opt = opt || {};
    if (!opt.potFile) {
        throw new PluginError(PLUGIN_NAME, "Missing potFile option.");
    }

    // Creating a stream through which each file will pass
    return through.obj(function (file, enc, cb) {
        const potFile = opt.potFile;
        const poFile = file.path;
        const poFileName = file.relative;
        const moFile = poFile.replace(/\.po$/, ".mo");
        const moFileName = poFileName.replace(/\.po$/, ".mo");

        log.info("Making PO: " + poFileName);
        execSync("msgmerge -v -q --no-location --backup=none --no-fuzzy-matching --update " + poFile + " " + potFile,
            function (err, stdout, stderr) {
                console.log(stdout);
                console.log(stderr);
                cb(err);
            });

        log.info("Making MO: " + moFileName);
        execSync("msgfmt -v -o " + moFile + " " + poFile,
            function (err, stdout, stderr) {
                console.log(stdout);
                console.log(stderr);
                cb(err);
            });

        this.push(file);
        cb();
    });
};
