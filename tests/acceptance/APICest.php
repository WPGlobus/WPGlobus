<?php

/**
 * Various function calls done by @see WPGlobus_QA are shown on the /api-demo/ page.
 * Here, we parse that page and verify that all functions work correctly.
 */
class APICest {

	/**
	 * @see WPGlobus_QA::_common_for_all_languages()
	 *
	 * @param AcceptanceTester $I
	 */
	protected function _common_for_all_languages( AcceptanceTester $I ) {
		$I->see( '{:en}ENG{:}{:ru}РУС{:}', '#tag_text' );
		$I->assertEquals( 'ENG РУС', $I->grabTextFrom( '#filter__the_title__' . 'no_tags' . ' .filter__the_title__output' ) );
		$I->assertEquals( 'ENG', $I->grabTextFrom( '#filter__the_title__' . 'one_tag' . ' .filter__the_title__output' ) );
		$I->assertEquals( 'ENG', $I->grabTextFrom( '#filter__the_title__' . 'one_tag_qt_tags' . ' .filter__the_title__output' ) );

		/**
		 * @see WPGlobus_QA::_test_get_the_terms()
		 */
		$I->assertEquals( 'boolean', $I->grabTextFrom( '#_test_get_the_terms' .
		                                               ' .non-existing-post-id' ) );
		$I->assertEquals( 'WP_Error', $I->grabTextFrom( '#_test_get_the_terms' .
		                                                ' .no-such-term' ) );

		/**
		 * @see WPGlobus_QA::_test_post_name
		 */
		$I->assertEquals( '', $I->grabTextFrom( '#_test_post_name' .
		                                        ' .wpg_qa_draft .wpg_qa_post_name' ) );
		$I->assertEquals( 'post-en', $I->grabTextFrom( '#_test_post_name' .
		                                        ' .wpg_qa_draft .wpg_qa_sample_permalink' ) );
		$I->assertEquals( 'post-en', $I->grabTextFrom( '#_test_post_name' .
		                                        ' .wpg_qa_publish .wpg_qa_post_name' ) );
		$I->assertEquals( 'post-en', $I->grabTextFrom( '#_test_post_name' .
		                                        ' .wpg_qa_publish .wpg_qa_sample_permalink' ) );
	}

	/**
	 * @see WPGlobus_QA::_test_home_url()
	 *
	 * @param AcceptanceTester $I
	 * @param string           $home_url
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
	 * Check the EN version of the api-demo page
	 *
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

		/**
		 * @see WPGlobus_QA::_test_get_pages()
		 */
		$I->assertEquals( "Contact Us", $I->grabTextFrom( '#test__get_pages__' . '72' ), 'test__get_pages' );

		/**
		 * @see WPGlobus_QA::_test_get_the_terms()
		 */
		$I->assertEquals( "Progress", $I->grabTextFrom(
			'#_test_get_the_terms' .
			' .test__get_the_terms__name' ),
			'test__get_the_terms__' );

		$I->assertEquals( "Development Progress", $I->grabTextFrom(
			'#_test_get_the_terms' .
			' .test__get_the_terms__description' ),
			'test__get_the_terms__' );

		/**
		 * @see WPGlobus_QA::_test_wp_get_object_terms()
		 */
		$I->assertEquals( "Progress", $I->grabTextFrom(
			'#_test_wp_get_object_terms_' . '6' . ' .name' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Test Category", $I->grabTextFrom(
			'#_test_wp_get_object_terms_' . '8' . ' .name' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Development Progress", $I->grabTextFrom(
			'#_test_wp_get_object_terms_' . '6' . ' .description' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Category for tests", $I->grabTextFrom(
			'#_test_wp_get_object_terms_' . '8' . ' .description' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Progress, Test Category", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .fields_names' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Invalid taxonomy", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .no_such_term' ),
			'test_wp_get_object_terms' );

		$this->_common_for_all_languages( $I );
	}

	/**
	 * Check the RU version of the api-demo page
	 *
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

		/**
		 * @see WPGlobus_QA::_test_get_pages()
		 */
		$I->assertEquals( "Обратная связь", $I->grabTextFrom( '#test__get_pages__' . '72' ), 'test__get_pages' );

		/**
		 * @see WPGlobus_QA::_test_get_the_terms()
		 */
		$I->assertEquals( "Прогресс", $I->grabTextFrom(
			'#_test_get_the_terms' .
			' .test__get_the_terms__name' ),
			'test__get_the_terms__' );

		$I->assertEquals( "Прогресс разработки", $I->grabTextFrom(
			'#_test_get_the_terms' .
			' .test__get_the_terms__description' ),
			'test__get_the_terms__' );

		/**
		 * @see WPGlobus_QA::_test_wp_get_object_terms()
		 */
		$I->assertEquals( "Прогресс", $I->grabTextFrom(
			'#_test_wp_get_object_terms_' . '6' . ' .name' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Тестовая категория", $I->grabTextFrom(
			'#_test_wp_get_object_terms_' . '8' . ' .name' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Прогресс разработки", $I->grabTextFrom(
			'#_test_wp_get_object_terms_' . '6' . ' .description' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Категория для тестов", $I->grabTextFrom(
			'#_test_wp_get_object_terms_' . '8' . ' .description' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Прогресс, Тестовая категория", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .fields_names' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Неверная таксономия", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .no_such_term' ),
			'test_wp_get_object_terms' );

		$this->_common_for_all_languages( $I );
	}

} // class

# --- EOF