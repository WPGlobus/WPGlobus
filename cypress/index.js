// ***********************************************************
// This example support/index.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import './commands'

// Alternatively you can use CommonJS syntax:
// require('./commands')

beforeEach(function () {
    cy.intercept({method: "POST", url: "/?wc-ajax=get_refreshed_fragments"}).as("fragments");
    cy.intercept({method: "POST", url: "/?wc-ajax=update_order_review"}).as("update_order_review");
    cy.intercept({method: "POST", url: "/?wc-ajax=checkout"}).as("checkout");
    cy.intercept({method: "POST", url: "/?wc-ajax=apply_coupon"}).as("apply_coupon");
    cy.intercept({method: "POST", url: "/?wc-ajax=remove_coupon"}).as("remove_coupon");
    cy.intercept({method: "POST", url: "/wp/wp-admin/admin-ajax.php"}).as("admin-ajax");
    cy.intercept({method: "POST", url: "/cart/"}).as("cart");

    cy.log('Clear cookies and local storage before every test in every spec file.');
    cy.clearCookies();
    cy.clearLocalStorage();
    window.sessionStorage.clear();
});
