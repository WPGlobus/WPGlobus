"use strict";

const gulp = require("gulp");
const cfg = require("./cfg.json");
const pkg = require('../package.json');
const replace = require('gulp-replace');
const log = require('fancy-log');

function taskReplaceVersion() {
    log.info(pkg.version);
    return gulp
        .src([pkg.name + ".php"])
        .pipe(replace(
            new RegExp(" \\* Version: .+"),
            " * Version: " + pkg.version
        ))
        .pipe(replace(
            new RegExp("define\\( '(" + cfg.version.define + ")'.+"),
            "define( '$1', '" + pkg.version + "' );"
        ))
        .pipe(gulp.dest("."));
}

module.exports = taskReplaceVersion;
