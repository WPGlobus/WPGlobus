"use strict";

module.exports = function () {
    var gulp = require("gulp");
    var cfg = require("./cfg.json");
    var pkg = require('../package.json');
    var tivwpPO = require("./gulp-tivwp-po");

    return gulp.src(cfg.path.languages + "/*.po")
        .pipe(tivwpPO({
            potFile: cfg.path.languages + "/" + pkg.name + ".pot"
        }));
};
