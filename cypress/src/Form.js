/// <reference types="cypress" />

import {FORCE, FORCE_NO_LOG} from "./CY/Options";

/**
 * Clear input field.
 *
 * @param {string} selector
 */
const inputClear = (selector) => {
    cy.get(selector).clear(FORCE);
};

/**
 * Set input field.
 *
 * @param {string} selector
 * @param {number|string} content
 */
const inputSet = (selector, content) => {
    cy.get(selector)
        .clear(FORCE_NO_LOG)
        .type(content.toString(), FORCE);
};

/**
 * Set input field.
 *
 * @param {string} selector
 * @param {number|string} content
 */
const inputSetNoLog = (selector, content) => {
    cy.get(selector, FORCE_NO_LOG)
        .clear(FORCE_NO_LOG)
        .type(content.toString(), FORCE_NO_LOG)
    ;
};

module.exports = {
    inputClear,
    inputSet,
    inputSetNoLog,
};
