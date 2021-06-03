"use strict";

const task_sass = cb => {
	const {src, dest} = require("gulp");
	const cfg = require("./cfg.json");
	const sass = require("gulp-dart-scss");
	const postcss = require("gulp-postcss");
	const keep_font_icons = require('postcss-sass-unicode');
	const autoprefixer = require("gulp-autoprefixer");
	const print = require('gulp-print').default;
	const pump = require('pump');

	const sassOptions = {
		outputStyle: "compressed"
		// outputStyle: "expanded"
	};

	const postcss_plugins = [
		keep_font_icons,
		autoprefixer
	];

	pump([
			src("includes/builders/gutenberg/assets/css/*.scss"),
			print(),
			sass(sassOptions),
			postcss(postcss_plugins),
			dest("includes/builders/gutenberg/assets/css"),
			print()
		],
	);

	pump([
			src("includes/builders/gutenberg/assets/css/dist/*.scss"),
			print(),
			sass(sassOptions),
			postcss(postcss_plugins),
			dest("includes/builders/gutenberg/assets/css/dist"),
			print()
		],
	);

	pump([
			src(cfg.path.css + "/**/*.scss"),
			print(),
			sass(sassOptions),
			postcss(postcss_plugins),
			dest(cfg.path.css),
			print()
		],
		cb
	);

};

module.exports = task_sass;
