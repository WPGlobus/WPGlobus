"use strict";

module.exports = function () {
    var gulp = require("gulp");
    var cfg = require("./cfg.json");
    var pkg = require('../package.json');
    var print = require('gulp-print').default;
    var zip = require('gulp-vinyl-zip');
    var destination = cfg.path.dist + "/" + pkg.name + "-" + pkg.version + ".zip";
    var log = require('fancy-log');

    log.info(destination);
    return gulp
        .src(cfg.src.zip, {base: "../"})
        .pipe(print())
        .pipe(zip.dest(destination))
        ;
};
