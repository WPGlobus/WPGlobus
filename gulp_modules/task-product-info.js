/*
 * Copyright (c) 2018. TIV.NET INC. / WPGlobus. All Rights Reserved.
 */

"use strict";

const {src, dest} = require("gulp");
const print = require('gulp-print').default;
const download = require("gulp-downloader");
const pump = require('pump');

const task_product_info = cb => {

	pump([
			download({
				fileName: "wpglobus-product-info.json",
				request: {
					url: "https://wpglobus.com/wc-api/wpglobus-product-info"
				}
			}),
			dest("data"),
			print()
		],
		cb
	);

};

module.exports = task_product_info;
