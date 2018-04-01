"use strict";

const gulp = require("gulp");
const cfg = require("./cfg.json");
const pkg = require('../package.json');
const pomo = require("./gulp-tivwp-po");

module.exports = function () {
    return gulp.src(cfg.path.languages + "/*.po")
        .pipe(pomo({
            potFile: cfg.path.languages + "/" + pkg.name + ".pot"
        }));
};
