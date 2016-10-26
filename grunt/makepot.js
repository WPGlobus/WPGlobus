module.exports = {
    target: {
        options: {
            mainFile: "<%= package.name %>.php",
            potHeaders: {
                poedit: true,
                "x-poedit-keywordslist": true
            },
            processPot: function (pot) {
                "use strict";
                pot.headers["report-msgid-bugs-to"] = "http://www.wpglobus.com/pg/contact-us/";
                pot.headers["language-team"] = "The WPGlobus Team <support@wpglobus.com>";
                pot.headers["last-translator"] = pot.headers["language-team"];
                delete pot.headers["x-generator"];
                return pot;
            },
            type: "wp-plugin",
            updateTimestamp: true,
            updatePoFiles: false
        }
    }
};
