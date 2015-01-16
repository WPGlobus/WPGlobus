<?php

/**
 *
 */
class APICest {

	/**
	 * @param AcceptanceTester $I
	 *
	 * @see WPGlobus_QA::_common_for_all_languages()
	 */
	protected function _common_for_all_languages( AcceptanceTester $I ) {
		$I->see( '{:en}ENG{:}{:ru}РУС{:}', '#tag_text' );
		$I->assertEquals( 'ENG РУС', $I->grabTextFrom( '#filter__the_title__' . 'no_tags' . ' .filter__the_title__output' ) );
		$I->assertEquals( 'ENG', $I->grabTextFrom( '#filter__the_title__' . 'one_tag' . ' .filter__the_title__output' ) );
		$I->assertEquals( 'ENG', $I->grabTextFrom( '#filter__the_title__' . 'one_tag_qt_tags' . ' .filter__the_title__output' ) );
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $home_url
	 *
	 * @see WPGlobus_QA::_test_home_url()
	 */
	protected function _test_home_url( AcceptanceTester $I, $home_url = '' ) {
		$I->see( $home_url, '#_test_home_url code' );
	}

	/**
	 * @see WPGlobus_QA::_test_string_parsing()
	 *
	 * @param AcceptanceTester $I
	 * @param string           $test_id
	 * @param string           $test_output
	 */
	protected function _test_string_parsing( AcceptanceTester $I, $test_id = '', $test_output = '' ) {
		$I->assertEquals( $test_output, $I->grabTextFrom( '#filter__the_title__' . $test_id . ' .filter__the_title__output' ) );
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $test_output
	 */
	protected function _test_string_parsing_ok( AcceptanceTester $I, $test_output = '' ) {
		foreach (
			[
				'proper',
				'proper_swap',
				'extra_lead',
				'extra_trail',
				'qt_tags_proper',
				'qt_tags_proper_swap',
				'qt_comments_proper',
				'qt_comments_proper_swap',
			]
			as $test_id
		) {
			$this->_test_string_parsing( $I, $test_id, $test_output );
		}

	}

	/**
	 * TESTS
	 * -----
	 */

	/**
	 * @param AcceptanceTester $I
	 */
	//	public function _before( AcceptanceTester $I ) {
	//	}


	/**
	 * @param AcceptanceTester $I
	 */
	public function en( AcceptanceTester $I ) {
		$I->amOnPage( '/api-demo/' );

		$I->see( 'api demo', 'h1' );

		$this->_test_home_url( $I, 'http://www.wpglobus.com' );

		$this->_test_string_parsing_ok( $I, 'ENG' );

		$I->assertEquals( "ENG1\nENG2", $I->grabTextFrom( '#filter__the_title__' . 'multiline' . ' .filter__the_title__output' ) );
		$I->assertEquals( "ENG1\nENG2", $I->grabTextFrom( '#filter__the_title__' . 'multiline_qt_tags' . ' .filter__the_title__output' ) );
		$I->assertEquals( "ENG1\nENG2", $I->grabTextFrom( '#filter__the_title__' . 'multiline_qt_comments' . ' .filter__the_title__output' ) );

		$I->assertEquals( "ENG1", $I->grabTextFrom( '#filter__the_title__' . 'multipart' . ' .filter__the_title__output' ) );

		$this->_common_for_all_languages( $I );
	}

	/**
	 * @param AcceptanceTester $I
	 */
	public function ru( AcceptanceTester $I ) {
		$I->amOnPage( '/ru/api-demo/' );

		/**
		 * Note: non-English texts should be entered here with the proper capitalization, as visible on the screen.
		 * Codeception does not apply UTF lowercase.
		 */
		$I->see( 'ДЕМОНСТРАЦИЯ API', 'h1' );

		$this->_test_home_url( $I, 'http://www.wpglobus.com/ru' );

		$this->_test_string_parsing_ok( $I, 'РУС' );

		$I->assertEquals( "РУС1\nРУС2", $I->grabTextFrom( '#filter__the_title__' . 'multiline' . ' .filter__the_title__output' ) );
		$I->assertEquals( "РУС1\nРУС2", $I->grabTextFrom( '#filter__the_title__' . 'multiline_qt_tags' . ' .filter__the_title__output' ) );
		$I->assertEquals( "РУС1\nРУС2", $I->grabTextFrom( '#filter__the_title__' . 'multiline_qt_comments' . ' .filter__the_title__output' ) );

		$I->assertEquals( "РУС1", $I->grabTextFrom( '#filter__the_title__' . 'multipart' . ' .filter__the_title__output' ) );

		$this->_common_for_all_languages( $I );
	}

} // class

# --- EOF