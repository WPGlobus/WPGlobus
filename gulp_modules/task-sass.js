"use strict";

const task_sass = cb => {
	const {src, dest} = require("gulp");
	const cfg = require("./cfg.json");
	const sass = require("gulp-sass");
	const sourcemaps = require("gulp-sourcemaps");
	const autoprefixer = require("gulp-autoprefixer");
	const print = require('gulp-print').default;
	const pump = require('pump');

	const sassOptions = {
		errLogToConsole: true,
		outputStyle: "compressed"
		// outputStyle: "expanded"
	};

	const autoprefixerOptions = {};

	pump([
			src("includes/builders/gutenberg/assets/css/*.scss"),
			print(),
			sourcemaps.init(),
			sass(sassOptions).on("error", sass.logError),
			autoprefixer(autoprefixerOptions),
			sourcemaps.write(".", {includeContent: false}),
			dest("includes/builders/gutenberg/assets/css"),
			print()
		],
	);

	pump([
			src("includes/builders/gutenberg/assets/css/dist/*.scss"),
			print(),
			sourcemaps.init(),
			sass(sassOptions).on("error", sass.logError),
			autoprefixer(autoprefixerOptions),
			sourcemaps.write(".", {includeContent: false}),
			dest("includes/builders/gutenberg/assets/css/dist"),
			print()
		],
	);

	pump([
			src(cfg.path.css + "/**/*.scss"),
			print(),
			sourcemaps.init(),
			sass(sassOptions).on("error", sass.logError),
			autoprefixer(autoprefixerOptions),
			sourcemaps.write(".", {includeContent: false}),
			dest(cfg.path.css),
			print()
		],
		cb
	);

};

module.exports = task_sass;
