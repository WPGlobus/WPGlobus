const USER_IDS = {
	admin: "1",
	buyer: "4"
};

/**
 * Log out.
 */
const Logout = () => {
	cy.visit("/?tivset-login=0", {log: false});
};

/**
 * Log in.
 * @param {string} user_id Default - admin.
 */
const LoginAs = (user_id = USER_IDS.admin) => {
	cy.visit(`/?tivset-login=${user_id}`, {log: true});
	cy.setCookie("tivwp", "wpgqa");
};

/**
 * Log in as Admin.
 */
const LoginAsAdmin = () => {
	LoginAs(USER_IDS.admin);
};

/**
 * Log in as Admin.
 */
const LoginAsBuyer = () => {
	LoginAs(USER_IDS.buyer);
};

module.exports = {
	LoginAs,
	LoginAsAdmin,
	LoginAsBuyer,
	Logout,
	USER_IDS
};
