"use strict";

module.exports = function () {
	var gulp = require("gulp");
	var wpPot = require("gulp-wp-pot");
	var cfg = require("./cfg.json");
	var pkg = require('../package.json');
	var log = require('fancy-log');
	var potFile = cfg.path.languages + "/" + pkg.name + ".pot";

	log.info(potFile);
	return gulp.src(["**/*.php", "!**/*Test.php", "!vendor/**/", "!wpsvn/**/"])
		.pipe(wpPot({
			domain: cfg.text_domain,
			package: pkg.title,
			bugReport: cfg.bugReport,
			headers: false,
			lastTranslator: pkg.author,
			relativeTo: ".",
			metadataFile: pkg.name + ".php"
		}))
		.pipe(gulp.dest(potFile));
};
