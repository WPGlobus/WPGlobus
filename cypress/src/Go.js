/// <reference types="cypress" />

const to = (url) => {
    cy.visit(url);
    cy.url().should('eq', Cypress.config().baseUrl + url);
    cy.get('head')
        .then((head) => {
            if (Cypress.$(head).find('link[rel="canonical"]').length) {
                cy.wrap(head).find('link[rel="canonical"]')
                    .should("have.attr", "href", Cypress.config().baseUrl + url);
            }
        });
};

const home = () => to("/");

module.exports = {
    to,
    home
};
