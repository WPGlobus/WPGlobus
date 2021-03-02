"use strict";

const task_bump_minor = cb => {
	const {src, dest} = require("gulp"),
		print = require('gulp-print').default,
		bump = require('gulp-bump'),
		pump = require('pump')
	;

	pump([
			src(['./package.json']),
			print(),
			bump({type:'minor'}),
			dest("./")
		],
		cb
	);
};

module.exports = task_bump_minor;
