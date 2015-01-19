<?php

/**
 * Class WPGlobus_QA
 */
class WPGlobus_QA {

	public static function api_demo() {
		?>
		<style>
			xmp {
				margin: 0;
			}
		</style>
		<?php
		self::_test_home_url();
		self::_test_string_parsing();
		self::_test_get_pages();
		self::_common_for_all_languages();
	}

	protected static function _common_for_all_languages() {
		?>
		<h2>Encode a text</h2>
		<p>Need to encode: <code>ENG, РУС</code></p>
		<p>Encoded string: <code id="tag_text"><?php
				echo ''
				     . WPGlobus::tag_text( 'ENG', 'en' )
				     . WPGlobus::tag_text( 'РУС', 'ru' );
				?></code>
		</p>
	<?php
	}

	protected static function _test_home_url() {
		?>
		<div id="<?php echo __FUNCTION__; ?>">
			<h2>home_url()</h2>
			<code><?php echo home_url(); ?></code>
		</div>
	<?php

	}

	protected static function _test_string_parsing() {
		?>

		<h2>Applying 'the_title' filter</h2>

		<?php

		$test_strings = [
			'proper'                  => '{:en}ENG{:}{:ru}РУС{:}',
			'proper_swap'             => '{:ru}РУС{:}{:en}ENG{:}',
			'extra_lead'              => 'Lead {:en}ENG{:}{:ru}РУС{:}',
			'extra_trail'             => '{:en}ENG{:}{:ru}РУС{:} Trail',
			'qt_tags_proper'          => '[:en]ENG[:ru]РУС',
			'qt_tags_proper_swap'     => '[:ru]РУС[:en]ENG',
			'qt_comments_proper'      => '<!--:en-->ENG<!--:--><!--:ru-->РУС<!--:-->',
			'qt_comments_proper_swap' => '<!--:ru-->РУС<!--:--><!--:en-->ENG<!--:-->',
			'multiline'               => "{:en}ENG1\nENG2{:}{:ru}РУС1\nРУС2{:}",
			'multiline_qt_tags'       => "[:en]ENG1\nENG2[:ru]РУС1\nРУС2",
			'multiline_qt_comments'   => "<!--:en-->ENG1\nENG2<!--:--><!--:ru-->РУС1\nРУС2<!--:-->",
			'no_tags'                 => 'ENG РУС',
			'one_tag'                 => '{:en}ENG{:}',
			'one_tag_qt_tags'         => '[:en]ENG',
			'multipart'               => '{:en}ENG1{:}{:ru}РУС1{:}{:en}ENG2{:}{:ru}РУС2{:}',
		];

		?>
		<table>
			<thead>
			<tr>
				<th>Input</th>
				<th>Output</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $test_strings as $test_id => $test_string ) : ?>
				<tr id="filter__the_title__<?php echo $test_id; ?>" title="filter__the_title__<?php echo $test_id; ?>">
					<td class="filter__the_title__input">
						<xmp><?php echo $test_string; ?></xmp>
					</td>
					<td class="filter__the_title__output">
						<xmp><?php echo apply_filters( 'the_title', $test_string ); ?></xmp>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php

	}

	/**
	 * To check the @see get_pages() function
	 * It is used, for example, to show a list of available pages in the "Parent Page" metabox
	 * when editing a page.
	 * Here, we display a list of first 3 pages
	 * and expect to see their titles correctly translated.
	 */
	private static function _test_get_pages() {

		/** @var WP_Post[] $all_pages */
		$all_pages = get_pages( [ 'number' => 3, 'sort_column' => 'ID' ] );

		?>
		<div id="<?php echo __FUNCTION__; ?>">
			<h2>get_pages()</h2>
			<?php foreach ( $all_pages as $page ) : ?>
				<div id="test__get_pages__<?php echo $page->ID; ?>">
					<?php echo $page->post_title; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php

	}

}

# --- EOF