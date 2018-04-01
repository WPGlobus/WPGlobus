const gulp = require("gulp");

gulp.task("readme", require("./gulp_modules/task-readme") );

gulp.task("replace-version", require("./gulp_modules/task-replace-version"));
gulp.task("pot", ["replace-version"], require("./gulp_modules/task-pot"));
gulp.task("pomo", ["pot"], require("./gulp_modules/task-tivwp_pomo"));

gulp.task("sass", require("./gulp_modules/task-sass"));
// gulp.task("uglify", require("./gulp_modules/task-uglify"));
// gulp.task("dist", ["pomo", "sass", "uglify"], require("./gulp_modules/task-dist"));
gulp.task("dist", ["readme", "pomo"], require("./gulp_modules/task-dist"));

gulp.task("default", ["dist"]);
