"use strict";

const task_pomo = cb => {
	const {src} = require("gulp");
	const cfg = require("./cfg.json");
	const pkg = require('../package.json');
	const tivwpPO = require("./gulp-tivwp-po");
	const print = require('gulp-print').default;
	const pump = require('pump');

	pump([
			src(cfg.path.languages + "/*.po"),
			tivwpPO({
				potFile: cfg.path.languages + "/" + pkg.name + ".pot"
			}),
			// Must have (buggy tivwpPO?)
			print()
		],
		cb
	);

};

module.exports = task_pomo;
