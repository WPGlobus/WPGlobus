<?php
/**
 *
 */

class WPGlobus_language_edit {

	var $language_name = array();

	var $flags = array();

	/*
	 * Constructor
	 */
	function __construct() {

		$this->get_data();
		//error_log( print_r( $this->flags, true ) );
		$this->display_table();

	}

	function get_data() {
		global $WPGlobus_Config;
		$this->language_name = $WPGlobus_Config->language_name;

		$this->_get_flags();
	}

	function display_table() {
		?>
		<div class="wrap">
			<h2>Edit Language</h2>
			<form method="post" action="">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="language_code">Language code</label></th>
						<td>
							<input name="language_code" type="text" id="language_code" value="" class="regular-text" />
							<p class="description"><?php _e( '2-Letter ISO Language Code for the Language you want to insert. (Example: en)', '' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="flags">Language flag</label></th>
						<td>
							<select id="flags" style="width:300px;" class="populate">	<?php
								foreach( $this->flags as $code=>$name ) {	?>
									<option value="<?php echo $name; ?>"><?php echo $name; ?></option>	<?php
								}	?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="language_name">Name</label></th>
						<td><input name="language_name" type="text" id="language_name" value="" class="regular-text" />
							<p class="description">The Name of the language, which will be displayed on the site. (Example: English)</p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="locale">Locale</label></th>
						<td><input name="locale" type="text" id="locale" value="" class="regular-text" />
							<p class="description">PHP and Wordpress Locale for the language. (Example: en_US)</p></td>
					</tr>
				</table>
				<p class="submit"><input class="button button-primary" type="submit" name="submit" value="Save Changes"></p>
			</form>
		</div>
		<?php
	}

	function _get_flags() {

		//$url = plugins_url(WPGlobus_Config::GLOBUS_PLUGIN_NAME . '/flags/');

		$path = WP_PLUGIN_DIR . '/' . WPGlobus_Config::GLOBUS_PLUGIN_NAME . '/flags/';

		$dir = new DirectoryIterator( $path );

		foreach ($dir as $file) {

			if ( $file->isFile() ) {

				$this->flags[] = $file->getFilename();
				//error_log( $file->getFilename() );
				//error_log( $file->getPathname() );

			}
		}

	}

}