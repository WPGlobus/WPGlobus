const
	{series} = require("gulp"),
	bump = require("./gulp_modules/task-bump"),
	bump_minor = require("./gulp_modules/task-bump-minor"),
	bump_major = require("./gulp_modules/task-bump-major"),
	readme = require("./gulp_modules/task-readme"),
	replace_version = require("./gulp_modules/task-replace-version"),
	make_pot = series(replace_version, require("./gulp_modules/task-pot")),
	// pot = series(make_pot, require("./gulp_modules/task-clean-pot")),
	pot = make_pot,
	pomo = series(pot, require("./gulp_modules/task-tivwp_pomo")),
	sass = require("./gulp_modules/task-sass"),
	uglify = require("./gulp_modules/task-uglify"),
	product_info = require("./gulp_modules/task-product-info"),
	dist = series(readme, sass, uglify, product_info, pomo)
;

exports.bump = bump;
exports.bump_minor = bump_minor;
exports.bump_major = bump_major;
exports.readme = readme;
exports.replace_version = replace_version;
exports.pot = pot;
exports.pomo = pomo;
exports.sass = sass;
exports.uglify = uglify;
exports.dist = dist;
exports.default = exports.dist;

