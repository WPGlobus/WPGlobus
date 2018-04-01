/*
 * Copyright (c) 2018. TIV.NET INC. / WPGlobus. All Rights Reserved.
 */

"use strict";

const gulp = require("gulp");
const wpPot = require("gulp-wp-pot");
const cfg = require("./cfg.json");
const pkg = require('../package.json');
const log = require('fancy-log');
const potFile = cfg.path.languages + "/" + pkg.name + ".pot";

module.exports = function () {
    log.info(potFile);
    return gulp.src(["**/*.php", "!**/*Test.php", "!vendor/**/"])
        .pipe(wpPot({
            domain: cfg.text_domain,
            package: pkg.title + " " + pkg.version,
            bugReport: cfg.bugReport,
            headers: false,
            lastTranslator: pkg.author,
            relativeTo: ".",
            metadataFile: pkg.name + ".php"
        }))
        .pipe(gulp.dest(potFile));
};
