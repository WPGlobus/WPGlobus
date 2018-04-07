"use strict";

module.exports = function () {
    var gulp = require("gulp");
    var cfg = require("./cfg.json");
    var pkg = require('../package.json');
    var replace = require('gulp-replace');
    var log = require('fancy-log');

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
};
