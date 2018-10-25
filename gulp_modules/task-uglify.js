"use strict";

module.exports = function (cb) {
    var gulp = require("gulp");
    var cfg = require("./cfg.json");
    var pkg = require('../package.json');
    var uglify = require("gulp-uglify");
    var rename = require("gulp-rename");
    var print = require('gulp-print').default;

    gulp
        .src([
            "includes/builders/assets/*.js",
            "!includes/**/*.min.js"
        ])
        .pipe(uglify())
        .pipe(rename({suffix: ".min"}))
        .pipe(gulp.dest("includes/builders/assets"))
        .pipe(print())
    ;

    gulp
        .src([
            "includes/builders/gutenberg/assets/js/*.js",
            "!includes/**/*.min.js"
        ])
        .pipe(uglify())
        .pipe(rename({suffix: ".min"}))
        .pipe(gulp.dest("includes/builders/gutenberg/assets/js"))
        .pipe(print())
    ;

    return gulp
        .src([cfg.path.js + "/**/*.js", "!" + cfg.path.js + "/**/*.min.js"])
        .pipe(uglify())
        .pipe(rename({suffix: ".min"}))
        .pipe(gulp.dest(cfg.path.js))
        .pipe(print())
        ;
};


// var pump = require('pump');
// pump([
//     gulp.src("includes/builders/assets/**/*.js"),
//     uglify(),
//     rename({suffix: ".min"}),
//     print()
// ], cb)
// ;
