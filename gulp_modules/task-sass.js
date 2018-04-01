/*
 * Copyright (c) 2018. TIV.NET INC. / WPGlobus. All Rights Reserved.
 */

"use strict";

const gulp = require("gulp");
const cfg = require("./cfg.json");
const sass = require("gulp-sass");
const sourcemaps = require("gulp-sourcemaps");
const autoprefixer = require("gulp-autoprefixer");
const print = require('gulp-print').default;

const sassOptions = {
    errLogToConsole: true,
    outputStyle: "compressed"
    // outputStyle: "expanded"
};

const autoprefixerOptions = {
    browsers: ["last 2 versions", "Firefox ESR", "ie 11"]
};

function taskSass() {
    return gulp
        .src(cfg.path.css + "/**/*.scss")
        .pipe(print())
        .pipe(sourcemaps.init())
        .pipe(sass(sassOptions).on("error", sass.logError))
        .pipe(autoprefixer(autoprefixerOptions))
        .pipe(sourcemaps.write(".", {includeContent: false}))
        .pipe(gulp.dest(cfg.path.css))
        .pipe(print())
        ;
}

module.exports = taskSass;
