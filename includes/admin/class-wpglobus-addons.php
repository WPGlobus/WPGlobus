<?php
/**
 * @package WPGlobus/Admin
 */

/**
 * Class WPGlobus_Addons
 */
class WPGlobus_Addons {

	/**
	 * Output the about screen.
	 */
	public static function addons_screen() {

		$addons = array();
		$addons['wordpress.org'][] = 'wpglobus';
		$addons['wordpress.org'][] = 'wpglobus-featured-images';
		
		/**
		 * @quirk
		 * Keeping this "wrap" only to display admin notice(s)
		 */
		?>
		<div class="wrap">

			<h2><?php
				/**
				 * @quirk
				 * This should be H2, so that it goes above the WP admin notices
				 */
				echo __( 'WPGlobus Add-ons/Extensions', 'wpglobus' );
				?></h2>


			<div class="wrap addons-wrap">

				<div class="addons-text">
					<?php //printf( __( 'Thank you for installing WPGlobus!', 'wpglobus' ), WPGLOBUS_VERSION ); ?>
				</div>
				<ul class="products">	<?php
					foreach( $addons as $source=>$addon ) {
						foreach( $addon as $addon_slug ) {
							$addon_data = self::get_addon($addon_slug, $source); 
							if ( $addon_data ) {	?>
								<li class="product">
									<a href="#">
										<h3><?php echo $addon_data->name; ?></h3>
										<p><?php echo $addon_data->short_description; ?></p>
									</a>	
								</li>	<?php
							}	
						}	
					} ?>
				</ul>

				<hr/>

				<div class="return-to-dashboard">
					<a href="admin.php?page=wpglobus_options">
						<?php _e( 'Go to WPGlobus Settings', 'wpglobus' ); ?>
					</a>
				</div>
			</div>

		</div>

		<?php
		/**
		 * @quirk
		 * Make the page longer to display the '#wpglobus-mini nicely'
		 */
		?>
		<div style="height: 20em">&nbsp;</div>
	<?php
	}

	/**
	 * Retrieve addon data
	 * return array|bool $data
	 *
	 * @param string $addon_slug
	 * @param string $source
	 *
	 * @return array|bool|mixed|stdClass
	 */
	public static function get_addon($addon_slug = '', $source = '') {
		
		if ( empty($addon_slug) ) {
			return false;
		}	
		
		$data = false;
		
		$cached = get_transient( 'wpglobus_addon_' . $addon_slug );
		if ( $cached !== false ) {
			return json_decode($cached);
		}
		
		if ( 'wordpress.org' == $source ) {
		
			$addon_json = wp_remote_get("https://api.wordpress.org/plugins/info/1.0/{$addon_slug}.json"); 
			if ( is_wp_error( $addon_json ) ) {
				$data = false;
			} else {	
				if ( 'null' == $addon_json['body'] ) {
					
					$addon = new stdClass();
					$addon->name = $addon_slug;
					$addon->short_description = 'Cannot retrieve data';
					return $addon;
				
				} else {	
					set_transient( 'wpglobus_addon_' . $addon_slug, $addon_json['body'] , 24 * HOUR_IN_SECONDS );
					$data = json_decode($addon_json['body']);
				}
			}	
		
		}	
		
		return $data;
	}	

} //class

# --- EOF