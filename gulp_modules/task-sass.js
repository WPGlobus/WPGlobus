"use strict";

module.exports = function () {
    var gulp = require("gulp");
    var cfg = require("./cfg.json");
    var sass = require("gulp-sass");
    var sourcemaps = require("gulp-sourcemaps");
    var autoprefixer = require("gulp-autoprefixer");
    var print = require('gulp-print').default;

    var sassOptions = {
        errLogToConsole: true,
        outputStyle: "compressed"
        // outputStyle: "expanded"
    };

    var autoprefixerOptions = {};

    gulp
        .src("includes/builders/gutenberg/assets/css/*.scss")
        .pipe(print())
        .pipe(sourcemaps.init())
        .pipe(sass(sassOptions).on("error", sass.logError))
        .pipe(autoprefixer(autoprefixerOptions))
        .pipe(sourcemaps.write(".", {includeContent: false}))
        .pipe(gulp.dest("includes/builders/gutenberg/assets/css"))
        .pipe(print())
        ;

    gulp
        .src("includes/builders/gutenberg/assets/css/dist/*.scss")
        .pipe(print())
        .pipe(sourcemaps.init())
        .pipe(sass(sassOptions).on("error", sass.logError))
        .pipe(autoprefixer(autoprefixerOptions))
        .pipe(sourcemaps.write(".", {includeContent: false}))
        .pipe(gulp.dest("includes/builders/gutenberg/assets/css/dist"))
        .pipe(print())
        ;

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
};
