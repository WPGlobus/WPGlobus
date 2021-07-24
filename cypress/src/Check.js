/*
 * Copyright (c) 2020, TIV.NET INC. All Rights Reserved.
 */

/**
 * Check all matching selector for content.
 *
 * @param {string[]} selectors
 * @param {string} content
 * @param {string} what
 */
const shouldWhat = (selectors, content, what) => {

	selectors.forEach(selector => cy.get(selector).should(what, content));

	// if (selectors.length === 1) {
	// 	cy.get(selectors[0]).should(what, content);
	// } else {
	// 	cy.get(selectors.join(","))
	// 		.each(($el, index, $list) => {
	// 			cy.wrap($el).should(what, content);
	// 		});
	// }

};

const shouldContain = (...args) => {
	shouldWhat(...args, "contain")
};

const shouldNotContain = (...args) => {
	shouldWhat(...args, "not.contain")
};

module.exports = {
	shouldContain,
	shouldNotContain
};
