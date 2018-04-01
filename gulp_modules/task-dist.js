"use strict";

const gulp = require("gulp");
const cfg = require("./cfg.json");
const pkg = require('../package.json');
const print = require('gulp-print').default;
const zip = require('gulp-vinyl-zip');
const destination = cfg.path.dist + "/" + pkg.name + "-" + pkg.version + ".zip";
const log = require('fancy-log');

function taskDist() {
    log.info(destination);
    return gulp
        .src(cfg.src.zip, {base: "../"})
        .pipe(print())
        .pipe(zip.dest(destination))
        ;
}
module.exports = taskDist;
