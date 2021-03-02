"use strict";

const task_uglify = cb => {
	const {src, dest} = require("gulp");
	const cfg = require("./cfg.json");
	const uglify = require("gulp-uglify");
	const rename = require("gulp-rename");
	const print = require('gulp-print').default;
	const pump = require('pump');
	const babel = require('gulp-babel');

	const paths_to_process = [
		"includes/vendor",
		"includes/builders/assets",
		"includes/builders/gutenberg/assets/js/dist",
		cfg.path.js
	];

	paths_to_process.forEach(function (path_js) {
		pump([
				src([`${path_js}/**/*.js`, `!${path_js}/**/*.min.js`], {base: path_js}),
				uglify(),
				rename({suffix: ".min"}),
				dest(path_js),
				print()
			],
		);
	});

	pump([
			src([
				"includes/builders/gutenberg/assets/js/*.js",
				"!includes/**/*.min.js"
			]),
			babel({presets: ['@babel/env']}),
			uglify(),
			rename({suffix: ".min"}),
			dest("includes/builders/gutenberg/assets/js"),
			print()
		],
		cb
	);
};

module.exports = task_uglify;
