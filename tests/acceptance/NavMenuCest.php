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
		$I->seeLink( 'CONTACT US' );
		$I->amOnPage( '/ru/' );
		$I->seeLink( 'КОНТАКТЫ' );
	}
} // class

# --- EOF