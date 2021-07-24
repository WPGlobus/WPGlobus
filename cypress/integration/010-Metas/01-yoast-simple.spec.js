/// <reference types="cypress" />

import {LoginAsAdmin, Logout} from "../../src/WP/Auth";
import {FORCE} from "../../src/CY/Options";

describe("Yoast Simple", () => {

	it("Puts correct metas", () => {

		cy.exec("wp plugin activate _tivset-url-login");

		LoginAsAdmin();
		// WPGlobus redirects after activation.
		cy.visit("/wp/wp-admin/");

		cy.visit("/wp/wp-admin/post-new.php?post_type=page");

		let title = "YoSi_T_EN";
		let desc = "YoSi_D_EN";

		cy.get("#post-title-1").type(title, FORCE);

		cy.get("#yoast-google-preview-description-metabox").type(desc, FORCE);

		cy.get(".editor-post-publish-button__button").click(FORCE);
		cy.get(".editor-post-publish-panel .editor-post-publish-button__button").click(FORCE);

		cy.visit("/wp/wp-admin/edit.php?post_type=page");
		cy.get('#post-search-input').type(title, FORCE);
		cy.get('#search-submit').click(FORCE);

		cy.get('.title > strong > .row-title').first().click(FORCE);

		// cy.get('[aria-label="WPGlobus Switcher"]').click(FORCE);
		cy.location("href").then((loc) => {
			cy.visit(loc + "&language=ru");
		});

		title = "YoSi_T_RU";
		desc = "YoSi_D_RU";

		cy.get("#post-title-1").type(title, FORCE);

		cy.get("#yoast-google-preview-description-metabox").type(desc, FORCE);

		cy.get(".editor-post-publish-button__button").click(FORCE);
		// cy.get(".editor-post-publish-panel .editor-post-publish-button__button").click(FORCE);

		cy.visit("/wp/wp-admin/edit.php?post_type=page");
		cy.get('#post-search-input').type(title, FORCE);
		cy.get('#search-submit').click(FORCE);

		cy.get('.title > strong > .row-title').first().click(FORCE);

		cy.visit("/yosi_t_en/");

		// cy.pause();

		// Logout();
	});
});
