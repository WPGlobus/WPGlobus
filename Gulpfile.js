const
	{series} = require("gulp"),
	readme = require("./gulp_modules/task-readme"),
	replace_version = require("./gulp_modules/task-replace-version"),
	pot = series(replace_version, require("./gulp_modules/task-pot")),
	pomo = series(pot, require("./gulp_modules/task-tivwp_pomo")),
	sass = require("./gulp_modules/task-sass"),
	uglify = require("./gulp_modules/task-uglify"),
	product_info = require("./gulp_modules/task-product-info"),
	dist = series(readme, sass, uglify, product_info, pomo)
;

exports.readme = readme;
exports.replace_version = replace_version;
exports.pot = pot;
exports.pomo = pomo;
exports.sass = sass;
exports.uglify = uglify;
exports.dist = dist;
exports.default = exports.dist;

