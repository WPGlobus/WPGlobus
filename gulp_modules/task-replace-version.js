"use strict";

const task_replace_version = cb => {
	const {src, dest} = require("gulp"),
		cfg = require("./cfg.json"),
		pkg = require('../package.json'),
		rpl = require('gulp-replace'),
		print = require('gulp-print').default,
		log = require('fancy-log'),
		pump = require('pump')
	;

	log.info(pkg.version);

	pump([
			src([pkg.name + ".php"]),
			print(),
			rpl(
				new RegExp("Version: .+"),
				"Version: " + pkg.version
			),
			rpl(
				new RegExp("define\\( '(" + cfg.version.define + ")'.+"),
				"define( '$1', '" + pkg.version + "' );"
			),
			dest(".")
		],
		cb
	);
};

module.exports = task_replace_version;
