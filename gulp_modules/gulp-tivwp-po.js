/*
 * Copyright (c) 2018. TIV.NET INC. / WPGlobus. All Rights Reserved.
 */

"use strict";

const execSync = require("child_process").execSync;
// through2 is a thin wrapper around node transform streams
const through = require("through2");
const PluginError = require('plugin-error');

const log = require('fancy-log');

// Consts
const PLUGIN_NAME = "gulp-tivwp-po";

// Plugin level function(dealing with files)
function tivwpPOMO(opt) {
    opt = opt || {};
    if (!opt.potFile) {
        throw new PluginError(PLUGIN_NAME, "Missing potFile option.");
    }

    // Creating a stream through which each file will pass
    return through.obj(function (file, enc, cb) {
        var potFile = opt.potFile;
        var poFile = file.path;
        var poFileName = file.relative;
        var moFile = poFile.replace(/\.po$/, ".mo");
        var moFileName = poFileName.replace(/\.po$/, ".mo");

        log.info("Making PO: " + poFileName);
        execSync("msgmerge -v --backup=none --no-fuzzy-matching --update " + poFile + " " + potFile,
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
}

module.exports = tivwpPOMO;
