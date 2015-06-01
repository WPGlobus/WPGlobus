<?php

/**
 * Class NavMenuCest
 */
class NavMenuCest {

	/**
	 * @param AcceptanceTester $I
	 */
	public function testMenuTextTranslation( AcceptanceTester $I ) {
		$I->amOnPage( '/' );
		$I->seeLink( WPGlobus_Acceptance::COMMON_PREFIX . ' menu_label ' . 'EN' );
		$I->amOnPage( '/ru/' );
		$I->seeLink( WPGlobus_Acceptance::COMMON_PREFIX . ' menu_label ' . 'RU' );
	}
} // class

# --- EOF