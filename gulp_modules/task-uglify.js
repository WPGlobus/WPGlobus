/*
 * Copyright (c) 2018. TIV.NET INC. / WPGlobus. All Rights Reserved.
 */

"use strict";

const gulp = require("gulp");
const cfg = require("./cfg.json");
const pkg = require('../package.json');
const uglify = require("gulp-uglify");
const rename = require("gulp-rename");
const print = require('gulp-print').default;


function taskUglify() {
    return gulp
        .src([cfg.path.js + "/**/*.js", "!" + cfg.path.js + "/**/*.min.js"])
        .pipe(uglify())
        .pipe(rename({suffix: ".min"}))
        .pipe(gulp.dest(cfg.path.js))
        .pipe(print())
        ;
}

module.exports = taskUglify;
