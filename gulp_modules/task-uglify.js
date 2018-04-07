"use strict";

module.exports = function () {
    var gulp = require("gulp");
    var cfg = require("./cfg.json");
    var pkg = require('../package.json');
    var uglify = require("gulp-uglify");
    var rename = require("gulp-rename");
    var print = require('gulp-print').default;

    return gulp
        .src([cfg.path.js + "/**/*.js", "!" + cfg.path.js + "/**/*.min.js"])
        .pipe(uglify())
        .pipe(rename({suffix: ".min"}))
        .pipe(gulp.dest(cfg.path.js))
        .pipe(print())
        ;
};
