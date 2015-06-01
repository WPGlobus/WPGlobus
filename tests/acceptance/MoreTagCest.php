<?php

/** */
class MoreTagCest {

	/**
	 * @covers WPGlobus_Filters::filter__the_posts()
	 *
	 * @param AcceptanceTester $I
	 */
	public function testMoreTag( AcceptanceTester $I ) {

		foreach ( array( 'en', 'ru' ) as $language ) {
			$language_prefix = $language === 'en' ? '' : '/' . $language;
			$language_suffix = strtoupper( $language );

			$I->amGoingTo( 'check the more tag in ' . $language_suffix );

			$I->amOnPage( $language_prefix . '/category/wpglobusqa-category-name-en/' );
			$I->assertContains(
				WPGlobus_Acceptance::COMMON_PREFIX . " post_content " . $language_suffix
				,
				$I->grabTextFrom( '.entry-content p' ), __LINE__ );
			$I->assertEquals(
				WPGlobus_Acceptance::COMMON_PREFIX . " post_title " . $language_suffix
				,
				$I->grabTextFrom( '.entry-content .screen-reader-text' ), __LINE__ );
		}
	}

} // class

# --- EOF