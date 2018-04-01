/*
 * Copyright (c) 2018. TIV.NET INC. / WPGlobus. All Rights Reserved.
 */

"use strict";

const gulp = require("gulp");
const print = require('gulp-print').default;
const download = require("gulp-downloader");


module.exports = function () {
    return download({
        fileName: "wpglobus-product-info.json",
        request: {
            url: "https://wpglobus.com/wc-api/wpglobus-product-info"
        }
    })
        .pipe(gulp.dest("data"))
        .pipe(print())
        ;
};
