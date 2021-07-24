/// <reference types="cypress" />

import {FORCE} from "../CY/Options";

const URL = "/cart/";
const BUTTON_ADD_TO_CART = ".single_add_to_cart_button";

const goTo = () => {
	cy.visit(URL);
};

/**
 * Add a single item to Cart from its page.
 */
const clickButtonAdd = function () {
	cy.get(BUTTON_ADD_TO_CART).click(FORCE);
	cy.wait("@fragments");
	cy.wait("@update_order_review");
	// cy.get(".woocommerce-message").should("contain", "added to your cart.");
};

module.exports = {
	URL,
	BUTTON_ADD_TO_CART,
	goTo,
	clickButtonAdd
};
