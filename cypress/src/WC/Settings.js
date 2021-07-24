/// <reference types="cypress" />

const setGeneral = (settings = [{}]) => {

	const {Admin, Constants, Go, Form} = require("../WOOMC");

	Admin.login();
	Go.to(Constants.URL_ADMIN + "?page=wc-settings");

	settings.forEach((setting) => {
		if ("input" === setting.type) {
			Form.inputSet(setting.selector, setting.value);
		}
	});

	cy.get(".woocommerce-save-button").click(Form.FORCE);

};

module.exports = {
	setGeneral
};
