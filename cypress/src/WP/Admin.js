/*
 * Copyright (c) 2020, TIV.NET INC. All Rights Reserved.
 */

import {FORCE_NO_LOG} from "../CY/Options";
import {inputSetNoLog} from "../Form";

/**
 * Login to admin area.
 */
const login = (user) => {
    cy.fixture("admin.json").then((admin) => {
        cy.visit(admin.url);
        cy.wait(1000);

        // If there is no login form then we are in admin already.
        cy.get("body").then(($body) => {
            if ($body.find("#user_login").length) {
                inputSetNoLog("#user_login", user);
                inputSetNoLog("#user_pass", admin.user[user].password);
                cy.get("#wp-submit", FORCE_NO_LOG).click(FORCE_NO_LOG);
                // cy.wait("@admin-ajax");
            }
        });
    });
};

/**
 * Log out of admin area.
 */
const logout = () => {
    // cy.get("#wp-admin-bar-logout a").click(FORCE_NO_LOG);
    // cy.visit("/?customer-logout=true");
    cy.visit("/my-account/customer-logout/");
    cy.get(".woocommerce-message > a").click({force: true});
};

module.exports = {
    login,
    logout
};
