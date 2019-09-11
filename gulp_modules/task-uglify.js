"use strict";

const task_uglify = cb => {
	const {src, dest} = require("gulp");
	const cfg = require("./cfg.json");
	const uglify = require("gulp-uglify");
	const rename = require("gulp-rename");
	const print = require('gulp-print').default;
	const pump = require('pump');
	const babel = require('gulp-babel');

	pump([
			src([
				"includes/builders/assets/*.js",
				"!includes/**/*.min.js"
			]),
			uglify(),
			rename({suffix: ".min"}),
			dest("includes/builders/assets"),
			print()
		],
	);

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
	);

	pump([
			src([
				"includes/builders/gutenberg/assets/js/dist/*.js",
				"!includes/**/*.min.js"
			]),
			uglify(),
			rename({suffix: ".min"}),
			dest("includes/builders/gutenberg/assets/js/dist"),
			print()
		],
	);

	pump([
			src([cfg.path.js + "/**/*.js", "!" + cfg.path.js + "/**/*.min.js"]),
			uglify(),
			rename({suffix: ".min"}),
			dest(cfg.path.js),
			print()
		],
		cb
	);
};

module.exports = task_uglify;


