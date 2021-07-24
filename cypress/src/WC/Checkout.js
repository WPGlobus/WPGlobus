/// <reference types="cypress" />

import {FORCE} from "../CY/Options";
import {inputSetNoLog} from "../Form";

const URL = "/checkout/";

const BUTTON_PLACE_ORDER= "#place_order";

const ADDRESS = {
    FIRST: "Tester",
    LAST: "Testov",
    COUNTRY: "Canada",
    STATE: "ON",
    POSTCODE: "L4C3S6",
    STREET: "1 Main St.",
    CITY: "Toronto",
    PHONE: "4161234567",
    EMAIL: "gregory@tiv.net",
};

const goTo = () => {
    cy.visit(URL);
};

/**
 * Fill out the checkout form.
 * @param {string} postcode Postcode is used to set a shipping method.
 * @param {string} currency Appended to the First Name.
 * @param {string} country Country [Canada].
 * @param {string} state State/Province abbreviation [ON].
 */
const fillForm = (
    {
        postcode = ADDRESS.POSTCODE,
        country = ADDRESS.COUNTRY,
        state = ADDRESS.STATE
    } = {}
) => {
    goTo();
    cy.wait("@update_order_review");
    cy.get("#billing_country").select(country, FORCE);
    cy.wait(2000);
    cy.wait("@update_order_review");
    cy.get("#billing_state").select(state, FORCE);
    cy.wait(2000);
    cy.get("#billing_state").should("have.value", state);

    inputSetNoLog("#billing_postcode", postcode);
    cy.wait(2000);

    inputSetNoLog("#billing_first_name", ADDRESS.FIRST);
    inputSetNoLog("#billing_last_name", ADDRESS.EMAIL);
    inputSetNoLog("#billing_address_1", ADDRESS.STREET);
    inputSetNoLog("#billing_city", ADDRESS.CITY);
    // inputSetNoLog("#billing_phone", ADDRESS.PHONE);
    inputSetNoLog("#billing_email", ADDRESS.EMAIL);

    cy.wait("@update_order_review");
};



const clickButtonPlaceOrder = () => {
	cy.get(BUTTON_PLACE_ORDER).click(FORCE);
	cy.wait("@checkout");
};


module.exports = {
    goTo,
    fillForm,
    clickButtonPlaceOrder
};
