<?php

/**
 * Class WPGlobus_QA
 */
class WPGlobus_QA {
	public static function api_demo() {
		?>
		<p><a href="<?php echo home_url(); ?>">home_url</a></p>

		<h2>Applying 'the_title' filter to a multilingual string</h2>
		<?php $text = '{:en}English{:}{:ru}Русский{:}'; ?>
		<p>Input: <code><?php echo $text; ?></code></p>
		<p>Output: <code id="filter__the_title"><?php echo apply_filters( 'the_title', $text ); ?></code></p>

		<h2>Tag a text</h2>
		<p>Input: <code>English, Русский</code></p>
		<p>Output: <code id="tag_text"><?php
				echo WPGlobus::tag_text( 'English', 'en' ) . WPGlobus::tag_text( 'Русский', 'ru' );
				?></code>
		</p>
	<?php
	}
}

# --- EOF