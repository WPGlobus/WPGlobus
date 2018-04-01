const gulp = require("gulp");
const replace = require('gulp-replace');
const rename = require("gulp-rename");
const gap = require('gulp-append-prepend');

gulp.task('readme', function () {

    const badges = "[![Latest Stable Version](https://poser.pugx.org/wpglobus/wpglobus/v/stable)](https://packagist.org/packages/wpglobus/wpglobus) [![Total Downloads](https://poser.pugx.org/wpglobus/wpglobus/downloads)](https://packagist.org/packages/wpglobus/wpglobus) [![Latest Unstable Version](https://poser.pugx.org/wpglobus/wpglobus/v/unstable)](https://packagist.org/packages/wpglobus/wpglobus) [![License](https://poser.pugx.org/wpglobus/wpglobus/license)](https://packagist.org/packages/wpglobus/wpglobus) [![Project Stats](https://www.openhub.net/p/WPGlobus/widgets/project_thin_badge.gif)](https://www.openhub.net/p/WPGlobus)\n";

    return gulp
        .src(["readme.txt"])

        // Replace the screenshots section with the link to WordPress repo.
        .pipe(replace(
            /== Screenshots ==\n+(?:\n.+)+\n+==(.+)==/m,
            "## Screenshots ##\n\nhttps://wordpress.org/plugins/wpglobus/#screenshots\n\n##$1##"
        ))

        // Insert newlines to the plugin header. Otherwise, they form one long line.
        .pipe(replace(/^(.+):/gm, "\n$1:"))

        // Replace heading marks.
        .pipe(replace(/=== (.+) ===/g, "# $1 #"))
        .pipe(replace(/== (.+) ==/g, "## $1 ##"))
        .pipe(replace(/= (.+) =/g, "### $1 ###"))

        // Link the contributors.
        .pipe(replace(
            "Contributors: tivnetinc, alexgff, tivnet",
            "Contributors: [TIV.NET INC.](https://profiles.wordpress.org/tivnetinc), " +
            "[Alex Gor](https://profiles.wordpress.org/alexgff), " +
            "[Gregory Karpinsky](https://profiles.wordpress.org/tivnet)"))

        // Prepend badges.
        .pipe(gap.prependText(badges))
        .pipe(rename("README.md"))
        .pipe(gulp.dest("."))
        ;
});
