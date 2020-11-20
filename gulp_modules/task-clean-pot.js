"use strict";

const task_clean_pot = cb => {
	const {src, dest} = require("gulp"),
		cfg = require("./cfg.json"),
		pkg = require('../package.json'),
		rpl = require('gulp-replace'),
		print = require('gulp-print').default,
		log = require('fancy-log'),
		pump = require('pump')
	;

	const regex = /#:.+\n/g;
	const subst = "";

	pump([
			src(["./languages/wpglobus.pot"]),
			print(),
			rpl(
				regex,
				subst
			),
			dest("./languages")
		],
		cb
	);
};

module.exports = task_clean_pot;
