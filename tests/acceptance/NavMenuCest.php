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
		$I->seeLink( WPGlobus_Acceptance::COMMON_PREFIX . ' page_title ' . 'EN' );
		$I->amOnPage( '/ru/' );
		$I->seeLink( WPGlobus_Acceptance::COMMON_PREFIX . ' page_title ' . 'RU' );
	}
} // class

# --- EOF