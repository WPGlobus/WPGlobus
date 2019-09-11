"use strict";

const task_pot = cb => {
	const {src, dest} = require("gulp");
	const wpPot = require("gulp-wp-pot");
	const cfg = require("./cfg.json");
	const pkg = require('../package.json');
	const log = require('fancy-log');
	const potFile = cfg.path.languages + "/" + pkg.name + ".pot";
	const print = require('gulp-print').default;
	const pump = require('pump');

	log.info("POT file: " + potFile);

	pump([
			src([
				"**/*.php",
				"!**/*Test.php",
				"!wpsvn/**/*",
				"!vendor/**/*"
			]),
			print(),
			wpPot({
				domain: cfg.text_domain,
				package: pkg.title + " " + pkg.version,
				bugReport: cfg.bugReport,
				headers: false,
				lastTranslator: pkg.author,
				relativeTo: ".",
				metadataFile: pkg.name + ".php"
			}),
			dest(potFile),
			print()
		],
		cb
	);
};

module.exports = task_pot;
